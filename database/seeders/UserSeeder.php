<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new User();
        $admin->insert([
            'name' => 'Mr. Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password')
        ]);
    }
}
