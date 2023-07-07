<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\AlkesSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Petugas',
            'email' => 'admintraxel@gmail.com',
            'password' => bcrypt('lpfkbjb123')
        ]);

        $this->call([
            AlkesSeeder::class
        ]);
    }
}
