<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_users')->insertOrIgnore([
            [
                'user_id'       => 1,
                'role_id'       => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]
        ]);
    }
}
