<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $users = [
            ['name' => 'Admin Demo',   'email' => 'admin@demo.test',   'role' => 'admin'],
            ['name' => 'Analyst Demo', 'email' => 'analyst@demo.test', 'role' => 'analyst'],
            ['name' => 'Client Demo',  'email' => 'client@demo.test',  'role' => 'client'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'role' => $u['role'],
                ]
            );
        }

        $this->call([
            CategorySeeder::class,
            ArticleSeeder::class,
            ResourceSeeder::class,
        ]);
    }
}
