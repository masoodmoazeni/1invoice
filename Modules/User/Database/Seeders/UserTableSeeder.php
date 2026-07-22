<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insertOrIgnore([
            [
                'firstname'       => 'masood',
                'lastname'        => 'moazeni',
                'email'           => 'm.moazeni68@gmail.com',
                'mobile'          => '77777777777',
                'password'        => Hash::make('123@123'),
                'active_email'    => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]
        ]);

        // ده کاربر رندم
        $users = [];

        for ($i = 1; $i <= 30; $i++) {
            $firstName = 'User' . $i;
            $lastName  = 'Test' . $i;
            $email     = 'user' . $i . '@example.com';
            $mobile    = '09' . rand(100000000, 999999999);

            $users[] = [
                'firstname'    => $firstName,
                'lastname'     => $lastName,
                'email'        => $email,
                'mobile'       => $mobile,
                'password'     => Hash::make('password123'),
                'active_email' => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        DB::table('users')->insertOrIgnore($users);
    }
}
