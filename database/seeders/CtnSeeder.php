<?php

namespace Database\Seeders;

use App\Models\Ctn;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CtnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ctn::factory()->count(10)->create();

    }
}
