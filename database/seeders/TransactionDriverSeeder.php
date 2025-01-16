<?php

namespace Database\Seeders;

use App\Models\TransactionDriver;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionDriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TransactionDriver::factory()->count(10)->create();
    }
}
