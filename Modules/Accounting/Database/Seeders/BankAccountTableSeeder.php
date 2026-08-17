<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class BankAccountTableSeeder extends Seeder
{
    /**
     * php artisan module:seed Accounting --class=BankAccountTableSeeder
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ابتدا بررسی می‌کنیم که حداقل یک شرکت و ارز وجود داشته باشد
        $companyId = DB::table('companies')->where('id', 1)->exists() ? 1 : null;
        $currencyId = DB::table('currencies')->where('id', 1)->exists() ? 1 : null;

        if (!$companyId || !$currencyId) {
            $this->command->warn('برای اجرای سیدر نیاز به حداقل یک شرکت و ارز دارید.');
            return;
        }

        $bankAccounts = [
            [
                'company_id' => $companyId,
                'bank_name' => 'بانک ملی',
                'branch_name' => 'شعبه مرکزی',
                'account_number' => '1234567890',
                'iban' => 'IR820540101680020000101001',
                'swift' => 'MELIIRTHXXX',
                'currency_id' => $currencyId,
                'is_default' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $companyId,
                'bank_name' => 'بانک ملت',
                'branch_name' => 'شعبه خیابان آزادی',
                'account_number' => '0987654321',
                'iban' => 'IR820120000000000000000001',
                'swift' => 'BMLIIRTHXXX',
                'currency_id' => $currencyId,
                'is_default' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $companyId,
                'bank_name' => 'بانک صادرات',
                'branch_name' => 'شعبه ولیعصر',
                'account_number' => '1122334455',
                'iban' => 'IR820190000000000000000002',
                'swift' => 'SADIRTHXXX',
                'currency_id' => $currencyId,
                'is_default' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $companyId,
                'bank_name' => 'بانک تجارت',
                'branch_name' => 'شعبه چهارراه ولیعصر',
                'account_number' => '5544332211',
                'iban' => 'IR820180000000000000000003',
                'swift' => 'TJRIIRTHXXX',
                'currency_id' => $currencyId,
                'is_default' => false,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_id' => $companyId,
                'bank_name' => 'بانک رفاه کارگران',
                'branch_name' => 'شعبه مرکزی',
                'account_number' => '6677889900',
                'iban' => 'IR820130000000000000000004',
                'swift' => 'REFAIRTHXXX',
                'currency_id' => $currencyId,
                'is_default' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        

        // درج داده‌ها
        foreach ($bankAccounts as $bankAccount) {
            // چک کردن وجود duplicate برای account_number + company_id
            $exists = DB::table('bank_accounts')
                ->where('company_id', $bankAccount['company_id'])
                ->where('account_number', $bankAccount['account_number'])
                ->exists();

            if (!$exists) {
                DB::table('bank_accounts')->insert($bankAccount);
            }
        }

        $this->command->info('سیدر حساب‌های بانکی با موفقیت اجرا شد.');
    }
}
