<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Hash;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Role::findOrCreate(config('filament-shield.panel_user.name', 'panel_user'));

        if (User::query()->where('is_admin', true)->doesntExist()) {
            User::factory()->create([
                'name' => 'Ahmed Alrashdy',
                'email' => 'admin@gmail.com',
                'is_admin' => true,
                'password' => Hash::make('12345678'),
            ]);
        }
        $this->callOnce([
            CategorySeeder::class,
            BrandSeeder::class,
        ]);

    }
}
