<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * پر کردن جدول companies با اطلاعات نمونه
     *
     * @return void
     */
    public function run()
    {
        // غیرفعال کردن بررسی کلیدهای خارجی برای جلوگیری از خطا
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // پاک کردن داده‌های موجود (اختیاری)
        DB::table('companies')->truncate();

        // فعال کردن مجدد بررسی کلیدهای خارجی
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // تعریف شرکت‌های نمونه
        $companies = [
            [
                'code' => 'COMP-001',
                'name' => 'شرکت البرز',
                'legal_name' => 'البرز صنعت پارس',
                'country_id' => 1, // ایران
                'base_currency_id' => 1, // ریال
                'language_id' => 1, // فارسی
                'timezone_id' => 1, // Asia/Tehran
                'tax_number' => '12345678901',
                'registration_number' => '12345',
                'phone' => '021-12345678',
                'mobile' => '09121234567',
                'email' => 'info@alborz.com',
                'website' => 'www.alborz.com',
                'address' => 'تهران، خیابان ولیعصر، پلاک ۱۲۳',
                'postal_code' => '1234567890',
                'city' => 'تهران',
                'state' => 'تهران',
                'logo' => 'uploads/logos/alborz.png',
                'fiscal_year_start_month' => 1, // شروع سال مالی از فروردین
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'COMP-002',
                'name' => 'شرکت سروش',
                'legal_name' => 'سروش تجارت آریا',
                'country_id' => 1, // ایران
                'base_currency_id' => 1, // ریال
                'language_id' => 1, // فارسی
                'timezone_id' => 1, // Asia/Tehran
                'tax_number' => '98765432109',
                'registration_number' => '67890',
                'phone' => '021-87654321',
                'mobile' => '09331234567',
                'email' => 'info@soroush.com',
                'website' => 'www.soroush.com',
                'address' => 'تهران، خیابان آزادی، پلاک ۴۵۶',
                'postal_code' => '9876543210',
                'city' => 'تهران',
                'state' => 'تهران',
                'logo' => 'uploads/logos/soroush.png',
                'fiscal_year_start_month' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'COMP-003',
                'name' => 'شرکت سپهر',
                'legal_name' => 'سپهر داده‌پرداز',
                'country_id' => 2, // آمریکا
                'base_currency_id' => 2, // دلار
                'language_id' => 2, // انگلیسی
                'timezone_id' => 5, // America/New_York
                'tax_number' => 'US-12345-678',
                'registration_number' => 'US-REG-001',
                'phone' => '+1-212-555-0123',
                'mobile' => '+1-212-555-0456',
                'email' => 'info@sepehr.com',
                'website' => 'www.sepehr.com',
                'address' => '123 Main Street, New York, NY 10001',
                'postal_code' => '10001',
                'city' => 'New York',
                'state' => 'NY',
                'logo' => 'uploads/logos/sepehr.png',
                'fiscal_year_start_month' => 1, // شروع سال مالی ژانویه
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'COMP-004',
                'name' => 'شرکت پویا',
                'legal_name' => 'پویا سیستم شرق',
                'country_id' => 1, // ایران
                'base_currency_id' => 1, // ریال
                'language_id' => 1, // فارسی
                'timezone_id' => 1, // Asia/Tehran
                'tax_number' => '11223344556',
                'registration_number' => '11223',
                'phone' => '051-12345678',
                'mobile' => '09151234567',
                'email' => 'info@pouya.com',
                'website' => 'www.pouya.com',
                'address' => 'مشهد، خیابان امام رضا، پلاک ۷۸۹',
                'postal_code' => '9876543210',
                'city' => 'مشهد',
                'state' => 'خراسان رضوی',
                'logo' => null,
                'fiscal_year_start_month' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'COMP-005',
                'name' => 'شرکت آریا',
                'legal_name' => 'آریا صنعت کویر',
                'country_id' => 3, // امارات
                'base_currency_id' => 3, // درهم
                'language_id' => 1, // فارسی
                'timezone_id' => 6, // Asia/Dubai
                'tax_number' => 'AE-12345-678',
                'registration_number' => 'AE-REG-001',
                'phone' => '+971-4-1234567',
                'mobile' => '+971-50-1234567',
                'email' => 'info@arya.com',
                'website' => 'www.arya.com',
                'address' => 'Dubai Marina, Dubai, UAE',
                'postal_code' => '12345',
                'city' => 'Dubai',
                'state' => 'Dubai',
                'logo' => 'uploads/logos/arya.png',
                'fiscal_year_start_month' => 1,
                'is_active' => false, // شرکت غیرفعال
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // درج داده‌ها در جدول
        foreach ($companies as $company) {
            DB::table('companies')->insert($company);
        }

        // یا می‌توانید از روش insert bulk استفاده کنید:
        // DB::table('companies')->insert($companies);
    }
}
