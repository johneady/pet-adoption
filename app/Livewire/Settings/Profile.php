<?php

namespace App\Livewire\Settings;

use App\Models\User;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
            'profilePicture' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
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

            // Resize and crop image using native GD
            $resizedImage = $this->resizeAndCropImage($this->profilePicture->getRealPath(), 150, 150);

            Storage::disk('public')->put($filename, $resizedImage);

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

    /**
     * Resize and crop image to cover the specified dimensions using native GD.
     */
    protected function resizeAndCropImage(string $sourcePath, int $targetWidth, int $targetHeight): string
    {
        // Get image info
        $imageInfo = getimagesize($sourcePath);
        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Create source image from file
        $sourceImage = match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => throw new \Exception('Unsupported image type'),
        };

        // Calculate dimensions for cover (crop to fill)
        $sourceAspect = $sourceWidth / $sourceHeight;
        $targetAspect = $targetWidth / $targetHeight;

        if ($sourceAspect > $targetAspect) {
            // Source is wider, crop width
            $newHeight = $sourceHeight;
            $newWidth = (int) ($sourceHeight * $targetAspect);
            $cropX = (int) (($sourceWidth - $newWidth) / 2);
            $cropY = 0;
        } else {
            // Source is taller, crop height
            $newWidth = $sourceWidth;
            $newHeight = (int) ($sourceWidth / $targetAspect);
            $cropX = 0;
            $cropY = (int) (($sourceHeight - $newHeight) / 2);
        }

        // Create destination image
        $destinationImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG and WebP
        if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
            imagealphablending($destinationImage, false);
            imagesavealpha($destinationImage, true);
        }

        // Resample (crop and resize)
        imagecopyresampled(
            $destinationImage,
            $sourceImage,
            0, 0,
            $cropX, $cropY,
            $targetWidth, $targetHeight,
            $newWidth, $newHeight
        );

        // Output to string
        ob_start();
        match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagejpeg($destinationImage, null, 90),
            'image/png' => imagepng($destinationImage, null, 9),
            'image/webp' => imagewebp($destinationImage, null, 90),
        };
        $imageData = ob_get_clean();

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($destinationImage);

        return $imageData;
    }

    public function render(): mixed
    {
        return view('livewire.settings.profile');
    }
}
