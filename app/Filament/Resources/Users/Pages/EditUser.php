<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Check if profile picture was removed (null or empty array)
        if (array_key_exists('profile_picture', $data) &&
            ($data['profile_picture'] === null || $data['profile_picture'] === [])) {
            $oldProfilePicture = $this->record->profile_picture;

            if ($oldProfilePicture) {
                Storage::disk('public')->delete($oldProfilePicture);
            }

            $data['profile_picture'] = null;
        }

        return $data;
    }
}
