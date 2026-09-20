<?php

namespace Modules\Company\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('companies')->updateOrInsert(
            ['code' => 'DEFAULT'],
            [
                'name' => 'Default Company',
                'is_active' => true,
                'fiscal_year_start_month' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
