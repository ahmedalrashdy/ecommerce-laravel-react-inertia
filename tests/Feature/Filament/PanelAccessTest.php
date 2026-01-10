<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_without_panel_access_cannot_enter_admin_panel(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_panel_users_can_enter_admin_panel(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $role = Role::findOrCreate('panel_user');
        $user->assignRole($role);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    public function test_admin_users_can_enter_admin_panel(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }
}
