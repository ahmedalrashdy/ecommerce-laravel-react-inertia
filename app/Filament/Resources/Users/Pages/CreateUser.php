<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Notifications\Auth\ForcedPasswordReset;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Password;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['is_admin']);

        if (! filled($data['password'] ?? null)) {
            $data['password'] = 'default-password';
        }

        $data['reset_password_required'] = true;

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = $this->getRecord();

        $token = Password::broker()->createToken($user);

        $user->notify(new ForcedPasswordReset($token));
    }
}
