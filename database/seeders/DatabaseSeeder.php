<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\AlkesSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AlkesSeeder::class
        ]);
    }
}
