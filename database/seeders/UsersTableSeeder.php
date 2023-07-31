<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::create([
            'name' => 'Admin', 
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin1')
        ]);
        // $users = User::where('id', 653)->first();

        $users->assignRole('super-admin');

        \fLogs($users);
    }
}