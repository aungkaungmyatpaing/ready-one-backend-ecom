<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $categories = [
            [
                'name' => 'Category A',
            ],
            [
                'name' => 'Category B',
            ],
            [
                'name' => 'Category C',

            ],
            [
                'name' => 'Category D',
            ],
        ];

        Category::insert($categories);
    }
}
