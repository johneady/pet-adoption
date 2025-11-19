<?php

namespace App\Actions\Fortify;

use App\Mail\NewUserRegistered;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'timezone' => Setting::get('default_timezone', 'America/Toronto'),
        ]);

        // Notify admins who have opted in to receive new user alerts
        $adminsToNotify = User::where('is_admin', true)
            ->where('receive_new_user_alerts', true)
            ->get();

        foreach ($adminsToNotify as $admin) {
            Mail::to($admin)->send(new NewUserRegistered($user));
        }

        return $user;
    }
}
