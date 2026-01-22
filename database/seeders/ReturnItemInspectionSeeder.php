<?php

namespace Database\Seeders;

use App\Models\ReturnItemInspection;
use Illuminate\Database\Seeder;

class ReturnItemInspectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReturnItemInspection::factory()
            ->count(5)
            ->create();
    }
}
