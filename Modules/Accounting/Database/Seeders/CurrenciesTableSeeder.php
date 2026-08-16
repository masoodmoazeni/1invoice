<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * php artisan module:seed Accounting --class=CurrenciesTableSeeder
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            // پاک کردن جدول (قبل از شروع تراکنش)
            DB::table('currencies')->truncate();

            // شروع تراکنش برای عملیات insert
            DB::beginTransaction();

            $currencies = [
                // ارزهای اصلی جهان
                ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'CNY', 'name' => 'Chinese Yuan', 'symbol' => '¥', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'Fr', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'C$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'symbol' => 'NZ$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'SEK', 'name' => 'Swedish Krona', 'symbol' => 'kr', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'NOK', 'name' => 'Norwegian Krone', 'symbol' => 'kr', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'DKK', 'name' => 'Danish Krone', 'symbol' => 'kr', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'symbol' => 'HK$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'KRW', 'name' => 'South Korean Won', 'symbol' => '₩', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'RUB', 'name' => 'Russian Ruble', 'symbol' => '₽', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => 'MX$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                
                // ارزهای خاورمیانه
                ['code' => 'IRR', 'name' => 'Iranian Rial', 'symbol' => '﷼', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => '﷼', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'QAR', 'name' => 'Qatari Riyal', 'symbol' => '﷼', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'OMR', 'name' => 'Omani Rial', 'symbol' => '﷼', 'decimal_places' => 3, 'rounding' => 0.001, 'is_active' => true],
                ['code' => 'KWD', 'name' => 'Kuwaiti Dinar', 'symbol' => 'د.ك', 'decimal_places' => 3, 'rounding' => 0.001, 'is_active' => true],
                ['code' => 'BHD', 'name' => 'Bahraini Dinar', 'symbol' => 'د.ب', 'decimal_places' => 3, 'rounding' => 0.001, 'is_active' => true],
                ['code' => 'JOD', 'name' => 'Jordanian Dinar', 'symbol' => 'د.ا', 'decimal_places' => 3, 'rounding' => 0.001, 'is_active' => true],
                ['code' => 'LBP', 'name' => 'Lebanese Pound', 'symbol' => 'ل.ل', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'EGP', 'name' => 'Egyptian Pound', 'symbol' => 'ج.م', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'TRY', 'name' => 'Turkish Lira', 'symbol' => '₺', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'ILS', 'name' => 'Israeli Shekel', 'symbol' => '₪', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'IQD', 'name' => 'Iraqi Dinar', 'symbol' => 'د.ع', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'LYD', 'name' => 'Libyan Dinar', 'symbol' => 'ل.د', 'decimal_places' => 3, 'rounding' => 0.001, 'is_active' => true],
                ['code' => 'TND', 'name' => 'Tunisian Dinar', 'symbol' => 'د.ت', 'decimal_places' => 3, 'rounding' => 0.001, 'is_active' => true],
                ['code' => 'DZD', 'name' => 'Algerian Dinar', 'symbol' => 'د.ج', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'MAD', 'name' => 'Moroccan Dirham', 'symbol' => 'د.م', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'AFN', 'name' => 'Afghan Afghani', 'symbol' => '؋', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'symbol' => '₨', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                
                // ارزهای آسیایی
                ['code' => 'THB', 'name' => 'Thai Baht', 'symbol' => '฿', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'IDR', 'name' => 'Indonesian Rupiah', 'symbol' => 'Rp', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'symbol' => 'RM', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'PHP', 'name' => 'Philippine Peso', 'symbol' => '₱', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'VND', 'name' => 'Vietnamese Dong', 'symbol' => '₫', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'TWD', 'name' => 'Taiwan Dollar', 'symbol' => 'NT$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'symbol' => '৳', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'LKR', 'name' => 'Sri Lankan Rupee', 'symbol' => '₨', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'NPR', 'name' => 'Nepalese Rupee', 'symbol' => '₨', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'KHR', 'name' => 'Cambodian Riel', 'symbol' => '៛', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'LAK', 'name' => 'Lao Kip', 'symbol' => '₭', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'MMK', 'name' => 'Myanmar Kyat', 'symbol' => 'K', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'MNT', 'name' => 'Mongolian Tugrik', 'symbol' => '₮', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'UZS', 'name' => 'Uzbekistani Som', 'symbol' => 'лв', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'KZT', 'name' => 'Kazakhstani Tenge', 'symbol' => '₸', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'GEL', 'name' => 'Georgian Lari', 'symbol' => '₾', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'AMD', 'name' => 'Armenian Dram', 'symbol' => '֏', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'AZN', 'name' => 'Azerbaijani Manat', 'symbol' => '₼', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                
                // ارزهای اروپایی (غیر از یورو)
                ['code' => 'PLN', 'name' => 'Polish Zloty', 'symbol' => 'zł', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'CZK', 'name' => 'Czech Koruna', 'symbol' => 'Kč', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'HUF', 'name' => 'Hungarian Forint', 'symbol' => 'Ft', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'RON', 'name' => 'Romanian Leu', 'symbol' => 'lei', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'BGN', 'name' => 'Bulgarian Lev', 'symbol' => 'лв', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'HRK', 'name' => 'Croatian Kuna', 'symbol' => 'kn', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'RSD', 'name' => 'Serbian Dinar', 'symbol' => 'дин', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'ALL', 'name' => 'Albanian Lek', 'symbol' => 'L', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'ISK', 'name' => 'Icelandic Krona', 'symbol' => 'kr', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'UAH', 'name' => 'Ukrainian Hryvnia', 'symbol' => '₴', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'BYN', 'name' => 'Belarusian Ruble', 'symbol' => 'Br', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'MDL', 'name' => 'Moldovan Leu', 'symbol' => 'L', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'MKD', 'name' => 'Macedonian Denar', 'symbol' => 'ден', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'BAM', 'name' => 'Bosnia-Herzegovina Convertible Mark', 'symbol' => 'KM', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'GIP', 'name' => 'Gibraltar Pound', 'symbol' => '£', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                
                // ارزهای آمریکای جنوبی
                ['code' => 'ARS', 'name' => 'Argentine Peso', 'symbol' => '$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'CLP', 'name' => 'Chilean Peso', 'symbol' => '$', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'COP', 'name' => 'Colombian Peso', 'symbol' => '$', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'PEN', 'name' => 'Peruvian Sol', 'symbol' => 'S/', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'VES', 'name' => 'Venezuelan Bolívar', 'symbol' => 'Bs', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'UYU', 'name' => 'Uruguayan Peso', 'symbol' => '$U', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'PYG', 'name' => 'Paraguayan Guarani', 'symbol' => '₲', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'BOB', 'name' => 'Bolivian Boliviano', 'symbol' => 'Bs', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'EC', 'name' => 'Ecuadorian Sucre (Historical)', 'symbol' => 'S/', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => false],
                
                // ارزهای آفریقایی
                ['code' => 'NGN', 'name' => 'Nigerian Naira', 'symbol' => '₦', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'KES', 'name' => 'Kenyan Shilling', 'symbol' => 'KSh', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'TZS', 'name' => 'Tanzanian Shilling', 'symbol' => 'TSh', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'UGX', 'name' => 'Ugandan Shilling', 'symbol' => 'USh', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'GHS', 'name' => 'Ghanaian Cedi', 'symbol' => '₵', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'MUR', 'name' => 'Mauritian Rupee', 'symbol' => '₨', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'ZMW', 'name' => 'Zambian Kwacha', 'symbol' => 'ZK', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'MGA', 'name' => 'Malagasy Ariary', 'symbol' => 'Ar', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'XOF', 'name' => 'West African CFA Franc', 'symbol' => 'CFA', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'XAF', 'name' => 'Central African CFA Franc', 'symbol' => 'FCFA', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'ETB', 'name' => 'Ethiopian Birr', 'symbol' => 'Br', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'AOA', 'name' => 'Angolan Kwanza', 'symbol' => 'Kz', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'MZN', 'name' => 'Mozambican Metical', 'symbol' => 'MT', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'SDG', 'name' => 'Sudanese Pound', 'symbol' => 'ج.س', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                
                // ارزهای اقیانوسیه
                ['code' => 'FJD', 'name' => 'Fiji Dollar', 'symbol' => 'FJ$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'PGK', 'name' => 'Papua New Guinean Kina', 'symbol' => 'K', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'SBD', 'name' => 'Solomon Islands Dollar', 'symbol' => 'SI$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'VUV', 'name' => 'Vanuatu Vatu', 'symbol' => 'Vt', 'decimal_places' => 0, 'rounding' => 1, 'is_active' => true],
                ['code' => 'WST', 'name' => 'Samoan Tala', 'symbol' => 'WS$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'TOP', 'name' => 'Tongan Paʻanga', 'symbol' => 'T$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                
                // ارزهای منطقه کارائیب
                ['code' => 'JMD', 'name' => 'Jamaican Dollar', 'symbol' => 'J$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'TTD', 'name' => 'Trinidad and Tobago Dollar', 'symbol' => 'TT$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'BSD', 'name' => 'Bahamian Dollar', 'symbol' => 'B$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'BBD', 'name' => 'Barbadian Dollar', 'symbol' => 'Bds$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'XCD', 'name' => 'East Caribbean Dollar', 'symbol' => 'EC$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'HTG', 'name' => 'Haitian Gourde', 'symbol' => 'G', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'DOP', 'name' => 'Dominican Peso', 'symbol' => 'RD$', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                ['code' => 'CUP', 'name' => 'Cuban Peso', 'symbol' => '₱', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => true],
                
                // ارزهای دیجیتال (اختیاری)
                ['code' => 'BTC', 'name' => 'Bitcoin', 'symbol' => '₿', 'decimal_places' => 8, 'rounding' => 0.00000001, 'is_active' => false],
                ['code' => 'ETH', 'name' => 'Ethereum', 'symbol' => 'Ξ', 'decimal_places' => 8, 'rounding' => 0.00000001, 'is_active' => false],
                ['code' => 'USDT', 'name' => 'Tether', 'symbol' => '₮', 'decimal_places' => 2, 'rounding' => 0.01, 'is_active' => false],
            ];

            // اضافه کردن timestamps
            $currenciesWithTimestamps = array_map(function($currency) {
                return array_merge($currency, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }, $currencies);

            // درج داده‌ها با تکه‌تکه کردن
            $chunkSize = 50;
            $chunks = array_chunk($currenciesWithTimestamps, $chunkSize);
            $insertedCount = 0;
            
            foreach ($chunks as $chunk) {
                if (!empty($chunk)) {
                    try {
                        DB::table('currencies')->insert($chunk);
                        $insertedCount += count($chunk);
                    } catch (Exception $e) {
                        // اگر خطا مربوط به تکراری بودن بود، یکی یکی درج کن
                        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            foreach ($chunk as $record) {
                                try {
                                    DB::table('currencies')->insert($record);
                                    $insertedCount++;
                                } catch (Exception $innerE) {
                                    Log::warning('Skipped duplicate currency: ' . $record['code']);
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

            $this->command->info("✅ Currencies seeded successfully!");
            $this->command->info("   📊 Inserted: {$insertedCount} records");

        } catch (Exception $e) {
            // در صورت خطا، rollback (اگر تراکنش فعال باشد)
            try {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
            } catch (Exception $rollbackException) {
                Log::warning("Rollback failed: " . $rollbackException->getMessage());
            }
            
            Log::error("Currencies Seeder failed: " . $e->getMessage());
            $this->command->error("❌ Seeding failed: " . $e->getMessage());
            throw $e;
        }
    }
}