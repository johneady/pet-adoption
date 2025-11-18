<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayPalIPNController extends Controller
{
    /**
     * Handle incoming PayPal IPN requests.
     */
    public function handleIPN(Request $request)
    {
        // Get the IPN message
        $ipnMessage = $request->all();

        // Verify the IPN with PayPal
        if (! $this->verifyIPN($request->getContent())) {
            Log::error('Invalid PayPal IPN', ['data' => $ipnMessage]);

            return response('Invalid IPN', 400);
        }

        // Check payment status
        $paymentStatus = $ipnMessage['payment_status'] ?? '';

        return match ($paymentStatus) {
            'Completed' => $this->handlePaymentCompleted($ipnMessage),
            'Refunded' => $this->handlePaymentRefunded($ipnMessage),
            'Reversed' => $this->handlePaymentReversed($ipnMessage),
            default => response('IPN handled', 200),
        };
    }

    /**
     * Verify the IPN message with PayPal.
     */
    protected function verifyIPN(string $rawPostData): bool
    {
        // Determine PayPal URL based on mode
        $paypalUrl = config('services.paypal.mode') === 'sandbox'
            ? 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://ipnpb.paypal.com/cgi-bin/webscr';

        // Post back to PayPal for verification
        $response = Http::asForm()->post($paypalUrl, [
            'cmd' => '_notify-validate',
            ...$_POST,
        ]);

        return $response->body() === 'VERIFIED';
    }

    /**
     * Handle completed payment.
     */
    protected function handlePaymentCompleted(array $ipnMessage): \Illuminate\Http\Response
    {
        // Extract custom data (JSON encoded user_id and plan_id)
        $customData = json_decode($ipnMessage['custom'] ?? '{}', true);
        $userId = $customData['user_id'] ?? null;
        $planId = $customData['plan_id'] ?? null;

        if (! $userId || ! $planId) {
            Log::error('Missing custom data in PayPal IPN', [
                'txn_id' => $ipnMessage['txn_id'] ?? 'unknown',
                'custom' => $ipnMessage['custom'] ?? '',
            ]);

            return response('Missing custom data', 400);
        }

        $user = User::find($userId);
        $plan = MembershipPlan::find($planId);

        if (! $user || ! $plan) {
            Log::error('User or plan not found for PayPal IPN', [
                'user_id' => $userId,
                'plan_id' => $planId,
                'txn_id' => $ipnMessage['txn_id'] ?? 'unknown',
            ]);

            return response('User or plan not found', 404);
        }

        // Check if this transaction was already processed (idempotency)
        $txnId = $ipnMessage['txn_id'] ?? null;
        if ($txnId && Membership::where('paypal_transaction_id', $txnId)->exists()) {
            Log::info('PayPal IPN already processed', ['txn_id' => $txnId]);

            return response('Already processed', 200);
        }

        // Verify the payment amount matches
        $paidAmount = (float) ($ipnMessage['mc_gross'] ?? 0);
        if ($paidAmount < $plan->price) {
            Log::error('Payment amount mismatch', [
                'expected' => $plan->price,
                'received' => $paidAmount,
                'txn_id' => $txnId,
            ]);

            return response('Payment amount mismatch', 400);
        }

        // Verify the receiver email
        $receiverEmail = $ipnMessage['receiver_email'] ?? '';
        $expectedEmail = config('services.paypal.email');
        if (strtolower($receiverEmail) !== strtolower($expectedEmail)) {
            Log::error('Receiver email mismatch', [
                'expected' => $expectedEmail,
                'received' => $receiverEmail,
                'txn_id' => $txnId,
            ]);

            return response('Receiver email mismatch', 400);
        }

        // Calculate dates
        $startedAt = now();
        $expiresAt = now()->addYear();

        // Create the membership
        $membership = Membership::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'amount_paid' => $paidAmount,
            'paypal_transaction_id' => $txnId,
            'started_at' => $startedAt,
            'expires_at' => $expiresAt,
        ]);

        // Create the transaction record
        MembershipTransaction::create([
            'membership_id' => $membership->id,
            'type' => 'payment',
            'amount' => $paidAmount,
            'payment_method' => 'paypal',
            'paypal_txn_id' => $txnId,
            'status' => 'completed',
        ]);

        // Update user's current membership
        $user->update(['current_membership_id' => $membership->id]);

        Log::info('Membership created successfully via PayPal', [
            'membership_id' => $membership->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'txn_id' => $txnId,
        ]);

        return response('IPN handled', 200);
    }

    /**
     * Handle refunded payment.
     */
    protected function handlePaymentRefunded(array $ipnMessage): \Illuminate\Http\Response
    {
        $parentTxnId = $ipnMessage['parent_txn_id'] ?? null;

        if (! $parentTxnId) {
            Log::warning('Refund IPN without parent transaction ID', $ipnMessage);

            return response('Missing parent transaction', 400);
        }

        // Find the membership by the original transaction
        $membership = Membership::where('paypal_transaction_id', $parentTxnId)->first();

        if ($membership) {
            $membership->refund();

            // Create refund transaction record
            MembershipTransaction::create([
                'membership_id' => $membership->id,
                'type' => 'refund',
                'amount' => abs((float) ($ipnMessage['mc_gross'] ?? 0)),
                'payment_method' => 'paypal',
                'paypal_txn_id' => $ipnMessage['txn_id'] ?? null,
                'status' => 'completed',
                'notes' => 'Refund processed via PayPal',
            ]);

            Log::info('Membership refunded via PayPal IPN', [
                'membership_id' => $membership->id,
                'parent_txn_id' => $parentTxnId,
                'refund_txn_id' => $ipnMessage['txn_id'] ?? null,
            ]);
        }

        return response('IPN handled', 200);
    }

    /**
     * Handle reversed payment (chargeback).
     */
    protected function handlePaymentReversed(array $ipnMessage): \Illuminate\Http\Response
    {
        $parentTxnId = $ipnMessage['parent_txn_id'] ?? null;

        if ($parentTxnId) {
            $membership = Membership::where('paypal_transaction_id', $parentTxnId)->first();

            if ($membership) {
                $membership->update(['status' => 'canceled']);

                Log::warning('Payment reversed (chargeback)', [
                    'membership_id' => $membership->id,
                    'parent_txn_id' => $parentTxnId,
                ]);
            }
        }

        return response('IPN handled', 200);
    }
}
