<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class LanguagesTableSeeder extends Seeder
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
            DB::table('languages')->truncate();

            // شروع تراکنش برای عملیات insert
            DB::beginTransaction();

            $languages = [
                // زبان‌های پرکاربرد
                ['code' => 'fa', 'name' => 'Persian', 'native_name' => 'فارسی', 'direction' => 'rtl', 'is_active' => true],
                ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'direction' => 'rtl', 'is_active' => true],
                ['code' => 'fr', 'name' => 'French', 'native_name' => 'Français', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'es', 'name' => 'Spanish', 'native_name' => 'Español', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'it', 'name' => 'Italian', 'native_name' => 'Italiano', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'pt', 'name' => 'Portuguese', 'native_name' => 'Português', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ru', 'name' => 'Russian', 'native_name' => 'Русский', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'zh', 'name' => 'Chinese', 'native_name' => '中文', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ja', 'name' => 'Japanese', 'native_name' => '日本語', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ko', 'name' => 'Korean', 'native_name' => '한국어', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ur', 'name' => 'Urdu', 'native_name' => 'اردو', 'direction' => 'rtl', 'is_active' => true],
                ['code' => 'tr', 'name' => 'Turkish', 'native_name' => 'Türkçe', 'direction' => 'ltr', 'is_active' => true],
                
                // زبان‌های اروپایی
                ['code' => 'nl', 'name' => 'Dutch', 'native_name' => 'Nederlands', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'el', 'name' => 'Greek', 'native_name' => 'Ελληνικά', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'sv', 'name' => 'Swedish', 'native_name' => 'Svenska', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'no', 'name' => 'Norwegian', 'native_name' => 'Norsk', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'da', 'name' => 'Danish', 'native_name' => 'Dansk', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'fi', 'name' => 'Finnish', 'native_name' => 'Suomi', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'pl', 'name' => 'Polish', 'native_name' => 'Polski', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'cs', 'name' => 'Czech', 'native_name' => 'Čeština', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'hu', 'name' => 'Hungarian', 'native_name' => 'Magyar', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ro', 'name' => 'Romanian', 'native_name' => 'Română', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'bg', 'name' => 'Bulgarian', 'native_name' => 'Български', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'sr', 'name' => 'Serbian', 'native_name' => 'Српски', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'hr', 'name' => 'Croatian', 'native_name' => 'Hrvatski', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'sk', 'name' => 'Slovak', 'native_name' => 'Slovenčina', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'sl', 'name' => 'Slovenian', 'native_name' => 'Slovenščina', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'lt', 'name' => 'Lithuanian', 'native_name' => 'Lietuvių', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'lv', 'name' => 'Latvian', 'native_name' => 'Latviešu', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'et', 'name' => 'Estonian', 'native_name' => 'Eesti', 'direction' => 'ltr', 'is_active' => true],
                
                // زبان‌های آسیایی
                ['code' => 'th', 'name' => 'Thai', 'native_name' => 'ไทย', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'vi', 'name' => 'Vietnamese', 'native_name' => 'Tiếng Việt', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'id', 'name' => 'Indonesian', 'native_name' => 'Bahasa Indonesia', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ms', 'name' => 'Malay', 'native_name' => 'Bahasa Melayu', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'fil', 'name' => 'Filipino', 'native_name' => 'Filipino', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'bn', 'name' => 'Bengali', 'native_name' => 'বাংলা', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ta', 'name' => 'Tamil', 'native_name' => 'தமிழ்', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'te', 'name' => 'Telugu', 'native_name' => 'తెలుగు', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'mr', 'name' => 'Marathi', 'native_name' => 'मराठी', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'pa', 'name' => 'Punjabi', 'native_name' => 'ਪੰਜਾਬੀ', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'gu', 'name' => 'Gujarati', 'native_name' => 'ગુજરાતી', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'kn', 'name' => 'Kannada', 'native_name' => 'ಕನ್ನಡ', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ml', 'name' => 'Malayalam', 'native_name' => 'മലയാളം', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ne', 'name' => 'Nepali', 'native_name' => 'नेपाली', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'si', 'name' => 'Sinhala', 'native_name' => 'සිංහල', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'km', 'name' => 'Khmer', 'native_name' => 'ខ្មែរ', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'lo', 'name' => 'Lao', 'native_name' => 'ລາວ', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'my', 'name' => 'Burmese', 'native_name' => 'မြန်မာ', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'mn', 'name' => 'Mongolian', 'native_name' => 'Монгол', 'direction' => 'ltr', 'is_active' => true],
                
                // زبان‌های خاورمیانه
                ['code' => 'he', 'name' => 'Hebrew', 'native_name' => 'עברית', 'direction' => 'rtl', 'is_active' => true],
                ['code' => 'ku', 'name' => 'Kurdish', 'native_name' => 'کوردی', 'direction' => 'rtl', 'is_active' => true],
                ['code' => 'ps', 'name' => 'Pashto', 'native_name' => 'پښتو', 'direction' => 'rtl', 'is_active' => true],
                ['code' => 'tg', 'name' => 'Tajik', 'native_name' => 'Тоҷикӣ', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'tk', 'name' => 'Turkmen', 'native_name' => 'Türkmençe', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'uz', 'name' => 'Uzbek', 'native_name' => 'Oʻzbekcha', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'kk', 'name' => 'Kazakh', 'native_name' => 'Қазақша', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ky', 'name' => 'Kyrgyz', 'native_name' => 'Кыргызча', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'az', 'name' => 'Azerbaijani', 'native_name' => 'Azərbaycanca', 'direction' => 'ltr', 'is_active' => true],
                
                // زبان‌های آفریقایی
                ['code' => 'af', 'name' => 'Afrikaans', 'native_name' => 'Afrikaans', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'sw', 'name' => 'Swahili', 'native_name' => 'Kiswahili', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ha', 'name' => 'Hausa', 'native_name' => 'Hausa', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'yo', 'name' => 'Yoruba', 'native_name' => 'Yorùbá', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ig', 'name' => 'Igbo', 'native_name' => 'Igbo', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'am', 'name' => 'Amharic', 'native_name' => 'አማርኛ', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'sn', 'name' => 'Shona', 'native_name' => 'chiShona', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'zu', 'name' => 'Zulu', 'native_name' => 'isiZulu', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'xh', 'name' => 'Xhosa', 'native_name' => 'isiXhosa', 'direction' => 'ltr', 'is_active' => true],
                
                // زبان‌های دیگر
                ['code' => 'ka', 'name' => 'Georgian', 'native_name' => 'ქართული', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'hy', 'name' => 'Armenian', 'native_name' => 'Հայերեն', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'sq', 'name' => 'Albanian', 'native_name' => 'Shqip', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'mk', 'name' => 'Macedonian', 'native_name' => 'Македонски', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'bs', 'name' => 'Bosnian', 'native_name' => 'Bosanski', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ca', 'name' => 'Catalan', 'native_name' => 'Català', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'gl', 'name' => 'Galician', 'native_name' => 'Galego', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'eu', 'name' => 'Basque', 'native_name' => 'Euskara', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'cy', 'name' => 'Welsh', 'native_name' => 'Cymraeg', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'ga', 'name' => 'Irish', 'native_name' => 'Gaeilge', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'gd', 'name' => 'Scottish Gaelic', 'native_name' => 'Gàidhlig', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'is', 'name' => 'Icelandic', 'native_name' => 'Íslenska', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'mt', 'name' => 'Maltese', 'native_name' => 'Malti', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'lb', 'name' => 'Luxembourgish', 'native_name' => 'Lëtzebuergesch', 'direction' => 'ltr', 'is_active' => true],
                
                // زبان‌های پراکنده
                ['code' => 'tl', 'name' => 'Tagalog', 'native_name' => 'Tagalog', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'jv', 'name' => 'Javanese', 'native_name' => 'Basa Jawa', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'su', 'name' => 'Sundanese', 'native_name' => 'Basa Sunda', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'mi', 'name' => 'Maori', 'native_name' => 'Māori', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'sm', 'name' => 'Samoan', 'native_name' => 'Gagana Samoa', 'direction' => 'ltr', 'is_active' => true],
                ['code' => 'to', 'name' => 'Tongan', 'native_name' => 'Lea faka-Tonga', 'direction' => 'ltr', 'is_active' => true],
            ];

            // اضافه کردن timestamps
            $languagesWithTimestamps = array_map(function($language) {
                return array_merge($language, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }, $languages);

            // درج داده‌ها با تکه‌تکه کردن
            $chunkSize = 50;
            $chunks = array_chunk($languagesWithTimestamps, $chunkSize);
            $insertedCount = 0;
            
            foreach ($chunks as $chunk) {
                if (!empty($chunk)) {
                    try {
                        DB::table('languages')->insert($chunk);
                        $insertedCount += count($chunk);
                    } catch (Exception $e) {
                        // اگر خطا مربوط به تکراری بودن بود، یکی یکی درج کن
                        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            foreach ($chunk as $record) {
                                try {
                                    DB::table('languages')->insert($record);
                                    $insertedCount++;
                                } catch (Exception $innerE) {
                                    Log::warning('Skipped duplicate language: ' . $record['code']);
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

            $this->command->info("✅ Languages seeded successfully!");
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
            
            Log::error("Languages Seeder failed: " . $e->getMessage());
            $this->command->error("❌ Seeding failed: " . $e->getMessage());
            throw $e;
        }
    }
}