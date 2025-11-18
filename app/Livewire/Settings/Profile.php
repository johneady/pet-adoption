<?php

namespace App\Livewire\Settings;

use App\Models\User;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public ?string $phone = null;

    public ?string $address = null;

    public string $timezone = 'America/Toronto';

    public ?TemporaryUploadedFile $profilePicture = null;

    public bool $removeProfilePicture = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->address = $user->address ?? '';
        $this->timezone = $user->timezone ?? 'America/Toronto';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'timezone' => ['required', 'string', 'timezone'],
            'profilePicture' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'timezone' => $validated['timezone'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($this->removeProfilePicture && $user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->profile_picture = null;
        }

        if ($this->profilePicture) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $filename = 'profile-pictures/'.uniqid().'.'.$this->profilePicture->getClientOriginalExtension();

            $image = Image::read($this->profilePicture->getRealPath());
            $image->cover(150, 150);

            Storage::disk('public')->put($filename, (string) $image->encode());

            $user->profile_picture = $filename;
        }

        $user->save();

        $this->reset(['profilePicture', 'removeProfilePicture']);

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Get the list of available timezones.
     *
     * @return array<string, string>
     */
    public function getTimezoneOptions(): array
    {
        return collect(DateTimeZone::listIdentifiers())
            ->mapWithKeys(fn ($tz) => [$tz => $tz])
            ->toArray();
    }

    public function render(): mixed
    {
        return view('livewire.settings.profile');
    }
}
