<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminUserCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_admin_user_with_default_flags(): void
    {
        $this->artisan('admin:create-user', [
            '--email' => 'admin-test@example.com',
            '--password' => 'super-secret-123',
            '--password-confirmation' => 'super-secret-123',
            '--name' => 'Admin Test',
            '--gender' => 'male',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admin-test@example.com',
            'name' => 'Admin Test',
            'gender' => 'male',
            'is_admin' => true,
            'is_active' => true,
            'reset_password_required' => false,
        ]);

        $user = User::query()->where('email', 'admin-test@example.com')->firstOrFail();

        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('super-secret-123', $user->password));
    }

    public function test_it_fails_when_the_email_already_exists(): void
    {
        User::factory()->create([
            'email' => 'existing-admin@example.com',
        ]);

        $this->artisan('admin:create-user', [
            '--email' => 'existing-admin@example.com',
            '--password' => 'super-secret-123',
            '--password-confirmation' => 'super-secret-123',
            '--name' => 'Existing Admin',
            '--gender' => 'female',
        ])
            ->assertExitCode(1);

        $this->assertSame(1, User::query()->where('email', 'existing-admin@example.com')->count());
    }
}
