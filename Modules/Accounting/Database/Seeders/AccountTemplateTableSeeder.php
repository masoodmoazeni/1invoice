<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class AccountTemplateTableSeeder extends Seeder
{
    /**
     * php artisan module:seed Accounting --class=AccountTemplateTableSeeder
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('account_templates')->truncate();
        Schema::enableForeignKeyConstraints();

        $templates = [
            // ایران - استاندارد حسابداری ایران
            [
                'country_id' => 1, // فرض می‌کنیم ID ایران = 1
                'name' => 'نمودار حساب‌های استاندارد ایران',
                'version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 1,
                'name' => 'نمودار حساب‌های شرکت‌های تولیدی ایران',
                'version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 1,
                'name' => 'نمودار حساب‌های شرکت‌های خدماتی ایران',
                'version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // آمریکا - استاندارد GAAP
            [
                'country_id' => 2, // فرض می‌کنیم ID آمریکا = 2
                'name' => 'US GAAP Chart of Accounts',
                'version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 2,
                'name' => 'US GAAP Manufacturing Chart of Accounts',
                'version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // انگلستان - استاندارد IFRS
            [
                'country_id' => 3, // فرض می‌کنیم ID انگلستان = 3
                'name' => 'UK IFRS Chart of Accounts',
                'version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'country_id' => 3,
                'name' => 'UK IFRS Retail Chart of Accounts',
                'version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // آلمان - استاندارد HGB
            [
                'country_id' => 4, // فرض می‌کنیم ID آلمان = 4
                'name' => 'German HGB Chart of Accounts',
                'version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // امارات متحده عربی
            [
                'country_id' => 5, // فرض می‌کنیم ID امارات = 5
                'name' => 'UAE IFRS Chart of Accounts',
                'version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // قالب بین‌المللی
            [
                'country_id' => 1, // قالب عمومی و بین‌المللی
                'name' => 'International Standard Chart of Accounts',
                'version' => '1.0.0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('account_templates')->insert($templates);
    }
}
