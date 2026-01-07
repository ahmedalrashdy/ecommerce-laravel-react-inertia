<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ManageUserRoles;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_toggle_admin_status_for_other_users(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->assignPanelRole($admin);
        $this->actingAs($admin);
        Filament::setCurrentPanel('admin');

        $user = User::factory()->create(['is_admin' => false]);

        Livewire::test(ListUsers::class)
            ->callTableAction('toggle_super_admin', $user);

        $this->assertTrue((bool) $user->refresh()->is_admin);

        Livewire::test(ListUsers::class)
            ->callTableAction('toggle_super_admin', $user);

        $this->assertFalse((bool) $user->refresh()->is_admin);
    }

    public function test_super_admin_cannot_toggle_own_admin_status(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->assignPanelRole($admin);
        $this->actingAs($admin);
        Filament::setCurrentPanel('admin');

        Livewire::test(ListUsers::class)
            ->assertTableActionHidden('toggle_super_admin', $admin);

        $this->assertTrue((bool) $admin->refresh()->is_admin);
    }

    public function test_roles_can_be_assigned_on_manage_user_roles_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->assignPanelRole($admin);
        $this->actingAs($admin);
        Filament::setCurrentPanel('admin');

        $roleA = Role::findOrCreate('manager');
        $roleB = Role::findOrCreate('editor');

        $user = User::factory()->create(['is_admin' => false]);

        Livewire::test(ManageUserRoles::class, ['record' => $user->getKey()])
            ->fillForm([
                'roles' => [$roleA->name, $roleB->name],
            ])
            ->call('save');

        $this->assertTrue($user->refresh()->hasAllRoles([$roleA->name, $roleB->name]));
    }

    private function assignPanelRole(User $user): void
    {
        $role = Role::findOrCreate('panel_user');

        $user->assignRole($role);
    }
}
