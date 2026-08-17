<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AccountTableSeeder extends Seeder
{
    /**
     * php artisan module:seed Accounting --class=AccountTableSeeder
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts')->truncate();

        $companyId = 1;

        $headerAccounts = [
            // دارایی‌ها (Assets)
            [
                'company_id' => $companyId,
                'account_code' => '1',
                'account_name' => 'دارایی‌ها',
                'parent_id' => null,
                'account_category' => 'asset',
                'account_type' => 'header',
                'normal_balance' => 'debit',
                'currency_id' => 1,
                'allow_posting' => false,
                'is_system' => true,
                'is_active' => true,
                'level' => 0,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // بدهی‌ها (Liabilities)
            [
                'company_id' => $companyId,
                'account_code' => '2',
                'account_name' => 'بدهی‌ها',
                'parent_id' => null,
                'account_category' => 'liability',
                'account_type' => 'header',
                'normal_balance' => 'credit',
                'currency_id' => 1,
                'allow_posting' => false,
                'is_system' => true,
                'is_active' => true,
                'level' => 0,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // سرمایه (Equity)
            [
                'company_id' => $companyId,
                'account_code' => '3',
                'account_name' => 'سرمایه',
                'parent_id' => null,
                'account_category' => 'equity',
                'account_type' => 'header',
                'normal_balance' => 'credit',
                'currency_id' => 1,
                'allow_posting' => false,
                'is_system' => true,
                'is_active' => true,
                'level' => 0,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // درآمدها (Revenue)
            [
                'company_id' => $companyId,
                'account_code' => '4',
                'account_name' => 'درآمدها',
                'parent_id' => null,
                'account_category' => 'revenue',
                'account_type' => 'header',
                'normal_balance' => 'credit',
                'currency_id' => 1,
                'allow_posting' => false,
                'is_system' => true,
                'is_active' => true,
                'level' => 0,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // هزینه‌ها (Expenses)
            [
                'company_id' => $companyId,
                'account_code' => '5',
                'account_name' => 'هزینه‌ها',
                'parent_id' => null,
                'account_category' => 'expense',
                'account_type' => 'header',
                'normal_balance' => 'debit',
                'currency_id' => 1,
                'allow_posting' => false,
                'is_system' => true,
                'is_active' => true,
                'level' => 0,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('accounts')->insert($headerAccounts);

        // دریافت ID حساب‌های کل
        $assetHeader = DB::table('accounts')->where('account_code', '1')->where('company_id', $companyId)->first();
        $liabilityHeader = DB::table('accounts')->where('account_code', '2')->where('company_id', $companyId)->first();
        $equityHeader = DB::table('accounts')->where('account_code', '3')->where('company_id', $companyId)->first();
        $revenueHeader = DB::table('accounts')->where('account_code', '4')->where('company_id', $companyId)->first();
        $expenseHeader = DB::table('accounts')->where('account_code', '5')->where('company_id', $companyId)->first();

        // تعریف حساب‌های جزئی (زیرمجموعه)
        $detailAccounts = [
            // دارایی‌های جاری
            [
                'company_id' => $companyId,
                'account_code' => '1.1',
                'account_name' => 'دارایی‌های جاری',
                'parent_id' => $assetHeader->id,
                'account_category' => 'asset',
                'account_type' => 'header',
                'normal_balance' => 'debit',
                'currency_id' => 1,
                'allow_posting' => false,
                'is_system' => true,
                'is_active' => true,
                'level' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $companyId,
                'account_code' => '1.1.1',
                'account_name' => 'صندوق',
                'parent_id' => null, // بعداً تنظیم می‌شود
                'account_category' => 'asset',
                'account_type' => 'detail',
                'normal_balance' => 'debit',
                'currency_id' => 1,
                'allow_posting' => true,
                'is_system' => false,
                'is_active' => true,
                'level' => 2,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $companyId,
                'account_code' => '1.1.2',
                'account_name' => 'بانک',
                'parent_id' => null, // بعداً تنظیم می‌شود
                'account_category' => 'asset',
                'account_type' => 'detail',
                'normal_balance' => 'debit',
                'currency_id' => 1,
                'allow_posting' => true,
                'is_system' => false,
                'is_active' => true,
                'level' => 2,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // بدهی‌های جاری
            [
                'company_id' => $companyId,
                'account_code' => '2.1',
                'account_name' => 'بدهی‌های جاری',
                'parent_id' => $liabilityHeader->id,
                'account_category' => 'liability',
                'account_type' => 'header',
                'normal_balance' => 'credit',
                'currency_id' => 1,
                'allow_posting' => false,
                'is_system' => true,
                'is_active' => true,
                'level' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $companyId,
                'account_code' => '2.1.1',
                'account_name' => 'حساب‌های پرداختنی',
                'parent_id' => null, // بعداً تنظیم می‌شود
                'account_category' => 'liability',
                'account_type' => 'detail',
                'normal_balance' => 'credit',
                'currency_id' => 1,
                'allow_posting' => true,
                'is_system' => false,
                'is_active' => true,
                'level' => 2,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // سرمایه
            [
                'company_id' => $companyId,
                'account_code' => '3.1',
                'account_name' => 'سرمایه صاحبان',
                'parent_id' => $equityHeader->id,
                'account_category' => 'equity',
                'account_type' => 'header',
                'normal_balance' => 'credit',
                'currency_id' => 1,
                'allow_posting' => false,
                'is_system' => true,
                'is_active' => true,
                'level' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $companyId,
                'account_code' => '3.1.1',
                'account_name' => 'سرمایه ثبت شده',
                'parent_id' => null, // بعداً تنظیم می‌شود
                'account_category' => 'equity',
                'account_type' => 'detail',
                'normal_balance' => 'credit',
                'currency_id' => 1,
                'allow_posting' => true,
                'is_system' => false,
                'is_active' => true,
                'level' => 2,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // درآمد
            [
                'company_id' => $companyId,
                'account_code' => '4.1',
                'account_name' => 'درآمد عملیاتی',
                'parent_id' => $revenueHeader->id,
                'account_category' => 'revenue',
                'account_type' => 'header',
                'normal_balance' => 'credit',
                'currency_id' => 1,
                'allow_posting' => false,
                'is_system' => true,
                'is_active' => true,
                'level' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $companyId,
                'account_code' => '4.1.1',
                'account_name' => 'درآمد فروش',
                'parent_id' => null, // بعداً تنظیم می‌شود
                'account_category' => 'revenue',
                'account_type' => 'detail',
                'normal_balance' => 'credit',
                'currency_id' => 1,
                'allow_posting' => true,
                'is_system' => false,
                'is_active' => true,
                'level' => 2,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // هزینه
            [
                'company_id' => $companyId,
                'account_code' => '5.1',
                'account_name' => 'هزینه‌های عملیاتی',
                'parent_id' => $expenseHeader->id,
                'account_category' => 'expense',
                'account_type' => 'header',
                'normal_balance' => 'debit',
                'currency_id' => 1,
                'allow_posting' => false,
                'is_system' => true,
                'is_active' => true,
                'level' => 1,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $companyId,
                'account_code' => '5.1.1',
                'account_name' => 'هزینه حقوق',
                'parent_id' => null, // بعداً تنظیم می‌شود
                'account_category' => 'expense',
                'account_type' => 'detail',
                'normal_balance' => 'debit',
                'currency_id' => 1,
                'allow_posting' => true,
                'is_system' => false,
                'is_active' => true,
                'level' => 2,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('accounts')->insert($detailAccounts);

        // به‌روزرسانی parent_id برای حساب‌های جزئی
        // دریافت ID حساب‌های میانی
        $currentAssets = DB::table('accounts')->where('account_code', '1.1')->where('company_id', $companyId)->first();
        $currentLiabilities = DB::table('accounts')->where('account_code', '2.1')->where('company_id', $companyId)->first();
        $equityCapital = DB::table('accounts')->where('account_code', '3.1')->where('company_id', $companyId)->first();
        $operatingRevenue = DB::table('accounts')->where('account_code', '4.1')->where('company_id', $companyId)->first();
        $operatingExpense = DB::table('accounts')->where('account_code', '5.1')->where('company_id', $companyId)->first();

        // به‌روزرسانی parent_id برای حساب‌های جزئی
        DB::table('accounts')
            ->where('account_code', '1.1.1')
            ->where('company_id', $companyId)
            ->update(['parent_id' => $currentAssets->id]);

        DB::table('accounts')
            ->where('account_code', '1.1.2')
            ->where('company_id', $companyId)
            ->update(['parent_id' => $currentAssets->id]);

        DB::table('accounts')
            ->where('account_code', '2.1.1')
            ->where('company_id', $companyId)
            ->update(['parent_id' => $currentLiabilities->id]);

        DB::table('accounts')
            ->where('account_code', '3.1.1')
            ->where('company_id', $companyId)
            ->update(['parent_id' => $equityCapital->id]);

        DB::table('accounts')
            ->where('account_code', '4.1.1')
            ->where('company_id', $companyId)
            ->update(['parent_id' => $operatingRevenue->id]);

        DB::table('accounts')
            ->where('account_code', '5.1.1')
            ->where('company_id', $companyId)
            ->update(['parent_id' => $operatingExpense->id]);
    }
}
