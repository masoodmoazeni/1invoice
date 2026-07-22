<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            [
                'name'  => 'admin',
                'label' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'broker',
                'label' => 'Broker',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
