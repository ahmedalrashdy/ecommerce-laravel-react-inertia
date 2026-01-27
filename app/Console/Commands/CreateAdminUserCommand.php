<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CreateAdminUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user
        {--email= : Admin email address}
        {--password= : Admin password}
        {--password-confirmation= : Admin password confirmation}
        {--name= : Admin full name}
        {--gender= : Admin gender (male|female)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a default admin user interactively';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->resolveEmail();
        $name = $this->resolveName();
        $gender = $this->resolveGender();
        $plainPassword = $this->resolvePassword();

        if (User::query()->where('email', $email)->exists()) {
            error('A user with this email already exists.');

            return self::FAILURE;
        }

        $user = new User;
        $user->forceFill([
            'name' => $name,
            'gender' => $gender,
            'email' => $email,
            'password' => $plainPassword,
            'is_admin' => true,
            'reset_password_required' => false,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $user->save();

        info("Admin user created successfully: {$user->email}");

        return self::SUCCESS;
    }

    private function resolveEmail(): string
    {
        $option = $this->option('email');

        if (is_string($option) && $option !== '') {
            $this->validateEmail($option);

            return $option;
        }

        return text(
            label: 'Admin email',
            placeholder: 'admin@example.com',
            validate: fn (string $value): ?string => $this->emailValidationMessage($value),
        );
    }

    private function resolveName(): string
    {
        $option = $this->option('name');

        if (is_string($option) && $option !== '') {
            $this->validateName($option);

            return $option;
        }

        return text(
            label: 'Admin name',
            placeholder: 'Jane Doe',
            validate: fn (string $value): ?string => $this->nameValidationMessage($value),
        );
    }

    private function resolveGender(): string
    {
        $option = $this->option('gender');

        if (is_string($option) && $option !== '') {
            $this->validateGender($option);

            return $option;
        }

        return select(
            label: 'Admin gender',
            options: [
                'male' => 'Male',
                'female' => 'Female',
            ],
            default: 'male',
        );
    }

    private function resolvePassword(): string
    {
        $passwordOption = $this->option('password');
        $confirmationOption = $this->option('password-confirmation');

        if (
            is_string($passwordOption) && $passwordOption !== '' &&
            is_string($confirmationOption) && $confirmationOption !== ''
        ) {
            $this->validatePasswordPair($passwordOption, $confirmationOption);

            return $passwordOption;
        }

        $plainPassword = password(
            label: 'Admin password',
            validate: fn (string $value): ?string => $this->passwordValidationMessage($value),
        );

        $confirmation = password(
            label: 'Confirm admin password',
        );

        $this->validatePasswordPair($plainPassword, $confirmation);

        return $plainPassword;
    }

    private function validateEmail(string $email): void
    {
        $this->runValidator(
            data: ['email' => $email],
            rules: [
                'email' => ['required', 'email', 'max:255'],
            ],
        );
    }

    private function validateName(string $name): void
    {
        $this->runValidator(
            data: ['name' => $name],
            rules: [
                'name' => ['required', 'string', 'min:2', 'max:255'],
            ],
        );
    }

    private function validateGender(string $gender): void
    {
        $this->runValidator(
            data: ['gender' => $gender],
            rules: [
                'gender' => ['required', Rule::in(['male', 'female'])],
            ],
        );
    }

    private function validatePasswordPair(string $plainPassword, string $confirmation): void
    {
        $this->runValidator(
            data: [
                'password' => $plainPassword,
                'password_confirmation' => $confirmation,
            ],
            rules: [
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
        );
    }

    private function emailValidationMessage(string $value): ?string
    {
        return $this->validationMessage(
            data: ['email' => $value],
            rules: [
                'email' => ['required', 'email', 'max:255'],
            ],
        );
    }

    private function nameValidationMessage(string $value): ?string
    {
        return $this->validationMessage(
            data: ['name' => $value],
            rules: [
                'name' => ['required', 'string', 'min:2', 'max:255'],
            ],
        );
    }

    private function passwordValidationMessage(string $value): ?string
    {
        return $this->validationMessage(
            data: [
                'password' => $value,
                'password_confirmation' => $value,
            ],
            rules: [
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $rules
     */
    private function validationMessage(array $data, array $rules): ?string
    {
        $validator = Validator::make($data, $rules);

        return $validator->fails()
            ? $validator->errors()->first()
            : null;
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $rules
     */
    private function runValidator(array $data, array $rules): void
    {
        $validator = Validator::make($data, $rules);

        $validator->validate();
    }
}
