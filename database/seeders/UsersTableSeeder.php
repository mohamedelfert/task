<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => 'admin',
            'phone' => '01153225410',
            'password' => bcrypt('123456'),
            'pin_code'  => null,
            'verified'  => 'yes',
            'role'  => 'admin',
        ]);
    }
}
