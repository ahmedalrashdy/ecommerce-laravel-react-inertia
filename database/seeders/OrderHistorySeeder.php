<?php

namespace Database\Seeders;

use App\Models\OrderHistory;
use Illuminate\Database\Seeder;

class OrderHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrderHistory::factory()
            ->count(10)
            ->create();
    }
}
