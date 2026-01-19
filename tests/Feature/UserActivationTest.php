<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_users_cannot_be_deactivated(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::test(ListUsers::class)
            ->callTableAction('toggle_active', $admin);

        $this->assertTrue((bool) $admin->refresh()->is_active);
    }

    public function test_non_admin_users_can_be_deactivated(): void
    {
        $this->actingAs($user = User::factory()->create(['is_admin' => true]));
        $this->assignPanelRole($user);
        Filament::setCurrentPanel('admin');

        $customer = User::factory()->create([
            'is_admin' => false,
            'is_active' => true,
        ]);

        Livewire::test(ListUsers::class)
            ->callTableAction('toggle_active', $customer);

        $this->assertFalse((bool) $customer->refresh()->is_active);
    }

    private function assignPanelRole(User $user): void
    {
        $role = Role::findOrCreate('panel_user');

        $user->assignRole($role);
    }
}
