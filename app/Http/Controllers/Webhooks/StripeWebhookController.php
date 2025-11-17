<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\MembershipTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    /**
     * Handle incoming Stripe webhook requests.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('cashier.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Invalid Stripe webhook payload', ['error' => $e->getMessage()]);

            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Invalid Stripe webhook signature', ['error' => $e->getMessage()]);

            return response('Invalid signature', 400);
        }

        // Handle the event
        return match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event),
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($event),
            'payment_intent.payment_failed' => $this->handlePaymentIntentFailed($event),
            default => response('Webhook handled', 200),
        };
    }

    /**
     * Handle successful checkout session completion.
     */
    protected function handleCheckoutSessionCompleted(\Stripe\Event $event): \Illuminate\Http\Response
    {
        $session = $event->data->object;

        // Extract metadata
        $userId = $session->metadata->user_id ?? null;
        $planId = $session->metadata->plan_id ?? null;
        $paymentType = $session->metadata->payment_type ?? 'annual';

        if (! $userId || ! $planId) {
            Log::error('Missing metadata in Stripe checkout session', [
                'session_id' => $session->id,
                'metadata' => $session->metadata,
            ]);

            return response('Missing metadata', 400);
        }

        $user = User::find($userId);
        $plan = MembershipPlan::find($planId);

        if (! $user || ! $plan) {
            Log::error('User or plan not found for Stripe checkout session', [
                'user_id' => $userId,
                'plan_id' => $planId,
                'session_id' => $session->id,
            ]);

            return response('User or plan not found', 404);
        }

        // Calculate dates
        $startedAt = now();
        $expiresAt = now()->addYear();
        $amount = $paymentType === 'annual' ? $plan->annual_price : $plan->monthly_price;

        // Create the membership
        $membership = Membership::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'payment_type' => $paymentType,
            'status' => 'active',
            'amount_paid' => $amount,
            'stripe_subscription_id' => $session->subscription ?? null,
            'stripe_payment_intent_id' => $session->payment_intent ?? null,
            'started_at' => $startedAt,
            'expires_at' => $expiresAt,
        ]);

        // Create the transaction record
        MembershipTransaction::create([
            'membership_id' => $membership->id,
            'type' => 'payment',
            'amount' => $amount,
            'payment_method' => 'stripe',
            'stripe_payment_id' => $session->payment_intent ?? $session->id,
            'status' => 'completed',
        ]);

        // Update user's current membership
        $user->update(['current_membership_id' => $membership->id]);

        Log::info('Membership created successfully', [
            'membership_id' => $membership->id,
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'session_id' => $session->id,
        ]);

        return response('Webhook handled', 200);
    }

    /**
     * Handle successful payment intent.
     */
    protected function handlePaymentIntentSucceeded(\Stripe\Event $event): \Illuminate\Http\Response
    {
        $paymentIntent = $event->data->object;

        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
        ]);

        return response('Webhook handled', 200);
    }

    /**
     * Handle failed payment intent.
     */
    protected function handlePaymentIntentFailed(\Stripe\Event $event): \Illuminate\Http\Response
    {
        $paymentIntent = $event->data->object;

        Log::warning('Payment intent failed', [
            'payment_intent_id' => $paymentIntent->id,
            'amount' => $paymentIntent->amount,
            'last_payment_error' => $paymentIntent->last_payment_error,
        ]);

        return response('Webhook handled', 200);
    }
}
