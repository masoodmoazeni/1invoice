<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TaxesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            // پاک کردن جدول (قبل از شروع تراکنش)
            DB::table('taxes')->truncate();

            // شروع تراکنش برای عملیات insert
            DB::beginTransaction();

            // دریافت کشورها
            $countries = DB::table('countries')
                ->select('id', 'iso2', 'iso3')
                ->get()
                ->keyBy('iso2');

            // دریافت شرکت‌ها (فرض می‌کنیم حداقل یک شرکت وجود دارد)
            $company = DB::table('companies')->first();
            
            if (!$company) {
                $this->command->warn("⚠️  No company found. Please create a company first.");
                $this->command->warn("   Using default company_id = 1");
                $companyId = 1;
            } else {
                $companyId = $company->id;
            }

            // دریافت حساب‌ها (فرض می‌کنیم حساب‌های پیش‌فرض وجود دارد)
            $defaultAccountId = 1; // می‌توانید از تنظیمات سیستم استفاده کنید

            $taxes = [
                // ============================================
                // ایران
                // ============================================
                [
                    'iso2' => 'IR',
                    'code' => 'VAT-IR',
                    'name' => 'مالیات بر ارزش افزوده',
                    'description' => 'مالیات بر ارزش افزوده ایران (VAT) - ۹ درصد',
                    'rate' => 9.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'IR',
                    'code' => 'INCOME-IR',
                    'name' => 'مالیات بر درآمد',
                    'description' => 'مالیات بر درآمد اشخاص حقوقی',
                    'rate' => 25.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'exclusive',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'IR',
                    'code' => 'VALUE-IR',
                    'name' => 'مالیات بر عوارض',
                    'description' => 'عوارض شهرداری و سایر عوارض',
                    'rate' => 3.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // امارات متحده عربی
                // ============================================
                [
                    'iso2' => 'AE',
                    'code' => 'VAT-AE',
                    'name' => 'VAT UAE',
                    'description' => 'Value Added Tax UAE - 5%',
                    'rate' => 5.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],

                // ============================================
                // عربستان سعودی
                // ============================================
                [
                    'iso2' => 'SA',
                    'code' => 'VAT-SA',
                    'name' => 'VAT Saudi',
                    'description' => 'Value Added Tax Saudi Arabia - 15%',
                    'rate' => 15.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],

                // ============================================
                // آمریکا
                // ============================================
                [
                    'iso2' => 'US',
                    'code' => 'SALES-US-FED',
                    'name' => 'Federal Sales Tax',
                    'description' => 'US Federal Sales Tax',
                    'rate' => 0.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'before_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => false,
                ],
                [
                    'iso2' => 'US',
                    'code' => 'SALES-US-CA',
                    'name' => 'California Sales Tax',
                    'description' => 'California State Sales Tax - 7.25%',
                    'rate' => 7.25,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'before_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'US',
                    'code' => 'SALES-US-NY',
                    'name' => 'New York Sales Tax',
                    'description' => 'New York State Sales Tax - 4%',
                    'rate' => 4.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'before_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // بریتانیا
                // ============================================
                [
                    'iso2' => 'GB',
                    'code' => 'VAT-UK',
                    'name' => 'VAT UK',
                    'description' => 'Value Added Tax United Kingdom - 20%',
                    'rate' => 20.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'GB',
                    'code' => 'VAT-UK-REDUCED',
                    'name' => 'VAT UK Reduced',
                    'description' => 'UK Reduced VAT Rate - 5%',
                    'rate' => 5.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // آلمان
                // ============================================
                [
                    'iso2' => 'DE',
                    'code' => 'VAT-DE',
                    'name' => 'VAT Germany',
                    'description' => 'Value Added Tax Germany - 19%',
                    'rate' => 19.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'DE',
                    'code' => 'VAT-DE-REDUCED',
                    'name' => 'VAT Germany Reduced',
                    'description' => 'Germany Reduced VAT Rate - 7%',
                    'rate' => 7.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // فرانسه
                // ============================================
                [
                    'iso2' => 'FR',
                    'code' => 'VAT-FR',
                    'name' => 'VAT France',
                    'description' => 'Value Added Tax France - 20%',
                    'rate' => 20.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'FR',
                    'code' => 'VAT-FR-REDUCED',
                    'name' => 'VAT France Reduced',
                    'description' => 'France Reduced VAT Rate - 5.5%',
                    'rate' => 5.50,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // ایتالیا
                // ============================================
                [
                    'iso2' => 'IT',
                    'code' => 'VAT-IT',
                    'name' => 'VAT Italy',
                    'description' => 'Value Added Tax Italy - 22%',
                    'rate' => 22.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'IT',
                    'code' => 'VAT-IT-REDUCED',
                    'name' => 'VAT Italy Reduced',
                    'description' => 'Italy Reduced VAT Rate - 10%',
                    'rate' => 10.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // اسپانیا
                // ============================================
                [
                    'iso2' => 'ES',
                    'code' => 'VAT-ES',
                    'name' => 'VAT Spain',
                    'description' => 'Value Added Tax Spain - 21%',
                    'rate' => 21.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'ES',
                    'code' => 'VAT-ES-REDUCED',
                    'name' => 'VAT Spain Reduced',
                    'description' => 'Spain Reduced VAT Rate - 10%',
                    'rate' => 10.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // ترکیه
                // ============================================
                [
                    'iso2' => 'TR',
                    'code' => 'VAT-TR',
                    'name' => 'KDV Turkey',
                    'description' => 'Katma Değer Vergisi Turkey - 18%',
                    'rate' => 18.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'TR',
                    'code' => 'VAT-TR-REDUCED',
                    'name' => 'KDV Turkey Reduced',
                    'description' => 'Turkey Reduced VAT Rate - 8%',
                    'rate' => 8.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // کانادا
                // ============================================
                [
                    'iso2' => 'CA',
                    'code' => 'GST-CA',
                    'name' => 'GST Canada',
                    'description' => 'Goods and Services Tax Canada - 5%',
                    'rate' => 5.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'CA',
                    'code' => 'PST-CA-BC',
                    'name' => 'PST British Columbia',
                    'description' => 'Provincial Sales Tax British Columbia - 7%',
                    'rate' => 7.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // استرالیا
                // ============================================
                [
                    'iso2' => 'AU',
                    'code' => 'GST-AU',
                    'name' => 'GST Australia',
                    'description' => 'Goods and Services Tax Australia - 10%',
                    'rate' => 10.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => true,
                    'is_default' => true,
                    'is_active' => true,
                ],

                // ============================================
                // ژاپن
                // ============================================
                [
                    'iso2' => 'JP',
                    'code' => 'VAT-JP',
                    'name' => 'Consumption Tax Japan',
                    'description' => 'Japan Consumption Tax - 10%',
                    'rate' => 10.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'JP',
                    'code' => 'VAT-JP-REDUCED',
                    'name' => 'Consumption Tax Japan Reduced',
                    'description' => 'Japan Reduced Consumption Tax - 8%',
                    'rate' => 8.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // چین
                // ============================================
                [
                    'iso2' => 'CN',
                    'code' => 'VAT-CN',
                    'name' => 'VAT China',
                    'description' => 'Value Added Tax China - 13%',
                    'rate' => 13.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'CN',
                    'code' => 'VAT-CN-REDUCED',
                    'name' => 'VAT China Reduced',
                    'description' => 'China Reduced VAT Rate - 6%',
                    'rate' => 6.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // هند
                // ============================================
                [
                    'iso2' => 'IN',
                    'code' => 'GST-IN',
                    'name' => 'GST India',
                    'description' => 'Goods and Services Tax India - 18%',
                    'rate' => 18.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'IN',
                    'code' => 'GST-IN-REDUCED',
                    'name' => 'GST India Reduced',
                    'description' => 'India Reduced GST Rate - 12%',
                    'rate' => 12.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // روسیه
                // ============================================
                [
                    'iso2' => 'RU',
                    'code' => 'VAT-RU',
                    'name' => 'VAT Russia',
                    'description' => 'Value Added Tax Russia - 20%',
                    'rate' => 20.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'RU',
                    'code' => 'VAT-RU-REDUCED',
                    'name' => 'VAT Russia Reduced',
                    'description' => 'Russia Reduced VAT Rate - 10%',
                    'rate' => 10.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // برزیل
                // ============================================
                [
                    'iso2' => 'BR',
                    'code' => 'ICMS-BR',
                    'name' => 'ICMS Brazil',
                    'description' => 'ICMS State VAT Brazil - 18%',
                    'rate' => 18.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => true,
                    'is_default' => true,
                    'is_active' => true,
                ],
                [
                    'iso2' => 'BR',
                    'code' => 'PIS-BR',
                    'name' => 'PIS Brazil',
                    'description' => 'PIS Social Contribution Brazil - 1.65%',
                    'rate' => 1.65,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'exclusive',
                    'is_inclusive' => false,
                    'is_default' => false,
                    'is_active' => true,
                ],

                // ============================================
                // مصر
                // ============================================
                [
                    'iso2' => 'EG',
                    'code' => 'VAT-EG',
                    'name' => 'VAT Egypt',
                    'description' => 'Value Added Tax Egypt - 14%',
                    'rate' => 14.00,
                    'tax_kind' => 'percentage',
                    'calculation_method' => 'after_discount',
                    'is_inclusive' => false,
                    'is_default' => true,
                    'is_active' => true,
                ],
            ];

            // تبدیل به فرمت قابل درج با country_id - فقط کشورهایی که وجود دارند
            $taxesWithIds = [];
            $insertedCount = 0;
            $skippedCount = 0;
            $skippedCountries = [];

            foreach ($taxes as $tax) {
                try {
                    

                    $taxesWithIds[] = [
                        'company_id' => 1,
                        'country_id' => 1,
                        'code' => $tax['code'],
                        'name' => $tax['name'],
                        'description' => $tax['description'],
                        'rate' => $tax['rate'],
                        'tax_kind' => $tax['tax_kind'],
                        'calculation_method' => $tax['calculation_method'],
                        'account_id' => $defaultAccountId,
                        'is_inclusive' => $tax['is_inclusive'],
                        'is_default' => $tax['is_default'],
                        'is_active' => $tax['is_active'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } catch (Exception $e) {
                    Log::warning("Error processing tax {$tax['code']}: " . $e->getMessage());
                    $skippedCount++;
                }
            }

            // اگر رکوردی برای درج وجود نداشت
            if (empty($taxesWithIds)) {
                $this->command->warn("⚠️  No taxes to insert. Please make sure countries exist in the database.");
                DB::commit();
                return;
            }

            // نمایش کشورهای رد شده
            if (!empty($skippedCountries)) {
                $this->command->warn("   ⚠️  Skipped " . count($skippedCountries) . " tax records for countries not found:");
                foreach (array_unique($skippedCountries) as $skipped) {
                    $this->command->warn("      - {$skipped}");
                }
            }

            // درج داده‌ها با تکه‌تکه کردن
            $chunkSize = 50;
            $chunks = array_chunk($taxesWithIds, $chunkSize);
            
            foreach ($chunks as $chunk) {
                if (!empty($chunk)) {
                    try {
                        DB::table('taxes')->insert($chunk);
                        $insertedCount += count($chunk);
                    } catch (Exception $e) {
                        // اگر خطا مربوط به تکراری بودن بود، یکی یکی درج کن
                        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            foreach ($chunk as $record) {
                                try {
                                    DB::table('taxes')->insert($record);
                                    $insertedCount++;
                                } catch (Exception $innerE) {
                                    Log::warning('Skipped duplicate tax: ' . $record['code']);
                                    $skippedCount++;
                                }
                            }
                        } else {
                            throw $e;
                        }
                    }
                }
            }

            // commit تراکنش
            DB::commit();

            $this->command->info("✅ Taxes seeded successfully!");
            $this->command->info("   📊 Inserted: {$insertedCount} records");
            $this->command->info("   ⏭️  Skipped: {$skippedCount} records");

        } catch (Exception $e) {
            // در صورت خطا، rollback (اگر تراکنش فعال باشد)
            try {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
            } catch (Exception $rollbackException) {
                Log::warning("Rollback failed: " . $rollbackException->getMessage());
            }
            
            Log::error("Taxes Seeder failed: " . $e->getMessage());
            $this->command->error("❌ Seeding failed: " . $e->getMessage());
            throw $e;
        }
    }
}