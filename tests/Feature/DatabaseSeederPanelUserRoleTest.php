<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseSeederPanelUserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_panel_user_role(): void
    {
        $this->seed(DatabaseSeeder::class);

        $roleName = config('filament-shield.panel_user.name', 'panel_user');

        $this->assertTrue(Role::query()->where('name', $roleName)->exists());
    }
}
