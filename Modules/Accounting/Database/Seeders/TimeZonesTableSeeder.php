<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Exception;

class TimeZonesTableSeeder extends Seeder
{
    /**
     * php artisan module:seed Accounting --class=TimeZonesTableSeeder
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::table('timezones')->truncate();

            DB::beginTransaction();

            // دریافت کشورها
            $countries = DB::table('countries')
                ->select('id', 'iso2')
                ->get()
                ->keyBy('iso2');

            // لیست مناطق زمانی (همان کد قبلی)
            $timeZones = [
                // افغانستان
                ['iso2' => 'AF', 'name' => 'Asia/Kabul', 'utc_offset' => '+04:30', 'is_default' => true],
                
                // آلبانی
                ['iso2' => 'AL', 'name' => 'Europe/Tirane', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'AL', 'name' => 'Europe/Tirane', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // الجزایر
                ['iso2' => 'DZ', 'name' => 'Africa/Algiers', 'utc_offset' => '+01:00', 'is_default' => true],
                
                // آندورا
                ['iso2' => 'AD', 'name' => 'Europe/Andorra', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'AD', 'name' => 'Europe/Andorra', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // آنگولا
                ['iso2' => 'AO', 'name' => 'Africa/Luanda', 'utc_offset' => '+01:00', 'is_default' => true],
                
                // آرژانتین
                ['iso2' => 'AR', 'name' => 'America/Argentina/Buenos_Aires', 'utc_offset' => '-03:00', 'is_default' => true],
                
                // ارمنستان
                ['iso2' => 'AM', 'name' => 'Asia/Yerevan', 'utc_offset' => '+04:00', 'is_default' => true],
                
                // استرالیا
                ['iso2' => 'AU', 'name' => 'Australia/Sydney', 'utc_offset' => '+10:00', 'is_default' => true],
                ['iso2' => 'AU', 'name' => 'Australia/Sydney', 'utc_offset' => '+11:00', 'is_default' => false],
                ['iso2' => 'AU', 'name' => 'Australia/Perth', 'utc_offset' => '+08:00', 'is_default' => false],
                ['iso2' => 'AU', 'name' => 'Australia/Adelaide', 'utc_offset' => '+09:30', 'is_default' => false],
                
                // اتریش
                ['iso2' => 'AT', 'name' => 'Europe/Vienna', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'AT', 'name' => 'Europe/Vienna', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // جمهوری آذربایجان
                ['iso2' => 'AZ', 'name' => 'Asia/Baku', 'utc_offset' => '+04:00', 'is_default' => true],
                
                // بحرین
                ['iso2' => 'BH', 'name' => 'Asia/Bahrain', 'utc_offset' => '+03:00', 'is_default' => true],
                
                // بنگلادش
                ['iso2' => 'BD', 'name' => 'Asia/Dhaka', 'utc_offset' => '+06:00', 'is_default' => true],
                
                // بلاروس
                ['iso2' => 'BY', 'name' => 'Europe/Minsk', 'utc_offset' => '+03:00', 'is_default' => true],
                
                // بلژیک
                ['iso2' => 'BE', 'name' => 'Europe/Brussels', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'BE', 'name' => 'Europe/Brussels', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // بولیوی
                ['iso2' => 'BO', 'name' => 'America/La_Paz', 'utc_offset' => '-04:00', 'is_default' => true],
                
                // برزیل
                ['iso2' => 'BR', 'name' => 'America/Sao_Paulo', 'utc_offset' => '-03:00', 'is_default' => true],
                ['iso2' => 'BR', 'name' => 'America/Sao_Paulo', 'utc_offset' => '-02:00', 'is_default' => false],
                ['iso2' => 'BR', 'name' => 'America/Manaus', 'utc_offset' => '-04:00', 'is_default' => false],
                ['iso2' => 'BR', 'name' => 'America/Fortaleza', 'utc_offset' => '-03:00', 'is_default' => false],
                
                // کانادا
                ['iso2' => 'CA', 'name' => 'America/Toronto', 'utc_offset' => '-05:00', 'is_default' => true],
                ['iso2' => 'CA', 'name' => 'America/Toronto', 'utc_offset' => '-04:00', 'is_default' => false],
                ['iso2' => 'CA', 'name' => 'America/Vancouver', 'utc_offset' => '-08:00', 'is_default' => false],
                ['iso2' => 'CA', 'name' => 'America/Vancouver', 'utc_offset' => '-07:00', 'is_default' => false],
                ['iso2' => 'CA', 'name' => 'America/Edmonton', 'utc_offset' => '-07:00', 'is_default' => false],
                ['iso2' => 'CA', 'name' => 'America/Halifax', 'utc_offset' => '-04:00', 'is_default' => false],
                
                // چین
                ['iso2' => 'CN', 'name' => 'Asia/Shanghai', 'utc_offset' => '+08:00', 'is_default' => true],
                ['iso2' => 'CN', 'name' => 'Asia/Urumqi', 'utc_offset' => '+06:00', 'is_default' => false],
                
                // کلمبیا
                ['iso2' => 'CO', 'name' => 'America/Bogota', 'utc_offset' => '-05:00', 'is_default' => true],
                
                // کرواسی
                ['iso2' => 'HR', 'name' => 'Europe/Zagreb', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'HR', 'name' => 'Europe/Zagreb', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // قبرس
                ['iso2' => 'CY', 'name' => 'Asia/Nicosia', 'utc_offset' => '+02:00', 'is_default' => true],
                ['iso2' => 'CY', 'name' => 'Asia/Nicosia', 'utc_offset' => '+03:00', 'is_default' => false],
                
                // جمهوری چک
                ['iso2' => 'CZ', 'name' => 'Europe/Prague', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'CZ', 'name' => 'Europe/Prague', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // دانمارک
                ['iso2' => 'DK', 'name' => 'Europe/Copenhagen', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'DK', 'name' => 'Europe/Copenhagen', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // مصر
                ['iso2' => 'EG', 'name' => 'Africa/Cairo', 'utc_offset' => '+02:00', 'is_default' => true],
                ['iso2' => 'EG', 'name' => 'Africa/Cairo', 'utc_offset' => '+03:00', 'is_default' => false],
                
                // فنلاند
                ['iso2' => 'FI', 'name' => 'Europe/Helsinki', 'utc_offset' => '+02:00', 'is_default' => true],
                ['iso2' => 'FI', 'name' => 'Europe/Helsinki', 'utc_offset' => '+03:00', 'is_default' => false],
                
                // فرانسه
                ['iso2' => 'FR', 'name' => 'Europe/Paris', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'FR', 'name' => 'Europe/Paris', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // آلمان
                ['iso2' => 'DE', 'name' => 'Europe/Berlin', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'DE', 'name' => 'Europe/Berlin', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // یونان
                ['iso2' => 'GR', 'name' => 'Europe/Athens', 'utc_offset' => '+02:00', 'is_default' => true],
                ['iso2' => 'GR', 'name' => 'Europe/Athens', 'utc_offset' => '+03:00', 'is_default' => false],
                
                // هنگ‌کنگ
                ['iso2' => 'HK', 'name' => 'Asia/Hong_Kong', 'utc_offset' => '+08:00', 'is_default' => true],
                
                // مجارستان
                ['iso2' => 'HU', 'name' => 'Europe/Budapest', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'HU', 'name' => 'Europe/Budapest', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // ایسلند
                ['iso2' => 'IS', 'name' => 'Atlantic/Reykjavik', 'utc_offset' => '+00:00', 'is_default' => true],
                
                // هند
                ['iso2' => 'IN', 'name' => 'Asia/Kolkata', 'utc_offset' => '+05:30', 'is_default' => true],
                
                // اندونزی
                ['iso2' => 'ID', 'name' => 'Asia/Jakarta', 'utc_offset' => '+07:00', 'is_default' => true],
                ['iso2' => 'ID', 'name' => 'Asia/Makassar', 'utc_offset' => '+08:00', 'is_default' => false],
                ['iso2' => 'ID', 'name' => 'Asia/Jayapura', 'utc_offset' => '+09:00', 'is_default' => false],
                
                // ایران
                ['iso2' => 'IR', 'name' => 'Asia/Tehran', 'utc_offset' => '+03:30', 'is_default' => true],
                ['iso2' => 'IR', 'name' => 'Asia/Tehran', 'utc_offset' => '+04:30', 'is_default' => false],
                
                // عراق
                ['iso2' => 'IQ', 'name' => 'Asia/Baghdad', 'utc_offset' => '+03:00', 'is_default' => true],
                
                // ایرلند
                ['iso2' => 'IE', 'name' => 'Europe/Dublin', 'utc_offset' => '+00:00', 'is_default' => true],
                ['iso2' => 'IE', 'name' => 'Europe/Dublin', 'utc_offset' => '+01:00', 'is_default' => false],
                
                // اسرائیل
                ['iso2' => 'IL', 'name' => 'Asia/Jerusalem', 'utc_offset' => '+02:00', 'is_default' => true],
                ['iso2' => 'IL', 'name' => 'Asia/Jerusalem', 'utc_offset' => '+03:00', 'is_default' => false],
                
                // ایتالیا
                ['iso2' => 'IT', 'name' => 'Europe/Rome', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'IT', 'name' => 'Europe/Rome', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // ژاپن
                ['iso2' => 'JP', 'name' => 'Asia/Tokyo', 'utc_offset' => '+09:00', 'is_default' => true],
                
                // اردن
                ['iso2' => 'JO', 'name' => 'Asia/Amman', 'utc_offset' => '+02:00', 'is_default' => true],
                ['iso2' => 'JO', 'name' => 'Asia/Amman', 'utc_offset' => '+03:00', 'is_default' => false],
                
                // قزاقستان
                ['iso2' => 'KZ', 'name' => 'Asia/Almaty', 'utc_offset' => '+06:00', 'is_default' => true],
                ['iso2' => 'KZ', 'name' => 'Asia/Aqtau', 'utc_offset' => '+05:00', 'is_default' => false],
                
                // کنیا
                ['iso2' => 'KE', 'name' => 'Africa/Nairobi', 'utc_offset' => '+03:00', 'is_default' => true],
                
                // کویت
                ['iso2' => 'KW', 'name' => 'Asia/Kuwait', 'utc_offset' => '+03:00', 'is_default' => true],
                
                // لبنان
                ['iso2' => 'LB', 'name' => 'Asia/Beirut', 'utc_offset' => '+02:00', 'is_default' => true],
                ['iso2' => 'LB', 'name' => 'Asia/Beirut', 'utc_offset' => '+03:00', 'is_default' => false],
                
                // لیبی
                ['iso2' => 'LY', 'name' => 'Africa/Tripoli', 'utc_offset' => '+02:00', 'is_default' => true],
                
                // مالزی
                ['iso2' => 'MY', 'name' => 'Asia/Kuala_Lumpur', 'utc_offset' => '+08:00', 'is_default' => true],
                
                // مکزیک
                ['iso2' => 'MX', 'name' => 'America/Mexico_City', 'utc_offset' => '-06:00', 'is_default' => true],
                ['iso2' => 'MX', 'name' => 'America/Mexico_City', 'utc_offset' => '-05:00', 'is_default' => false],
                ['iso2' => 'MX', 'name' => 'America/Tijuana', 'utc_offset' => '-08:00', 'is_default' => false],
                
                // مراکش
                ['iso2' => 'MA', 'name' => 'Africa/Casablanca', 'utc_offset' => '+00:00', 'is_default' => true],
                ['iso2' => 'MA', 'name' => 'Africa/Casablanca', 'utc_offset' => '+01:00', 'is_default' => false],
                
                // نپال
                ['iso2' => 'NP', 'name' => 'Asia/Kathmandu', 'utc_offset' => '+05:45', 'is_default' => true],
                
                // هلند
                ['iso2' => 'NL', 'name' => 'Europe/Amsterdam', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'NL', 'name' => 'Europe/Amsterdam', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // نیوزیلند
                ['iso2' => 'NZ', 'name' => 'Pacific/Auckland', 'utc_offset' => '+12:00', 'is_default' => true],
                ['iso2' => 'NZ', 'name' => 'Pacific/Auckland', 'utc_offset' => '+13:00', 'is_default' => false],
                
                // نیجریه
                ['iso2' => 'NG', 'name' => 'Africa/Lagos', 'utc_offset' => '+01:00', 'is_default' => true],
                
                // نروژ
                ['iso2' => 'NO', 'name' => 'Europe/Oslo', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'NO', 'name' => 'Europe/Oslo', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // عمان
                ['iso2' => 'OM', 'name' => 'Asia/Muscat', 'utc_offset' => '+04:00', 'is_default' => true],
                
                // پاکستان
                ['iso2' => 'PK', 'name' => 'Asia/Karachi', 'utc_offset' => '+05:00', 'is_default' => true],
                
                // فلسطین
                ['iso2' => 'PS', 'name' => 'Asia/Gaza', 'utc_offset' => '+02:00', 'is_default' => true],
                ['iso2' => 'PS', 'name' => 'Asia/Gaza', 'utc_offset' => '+03:00', 'is_default' => false],
                
                // پرو
                ['iso2' => 'PE', 'name' => 'America/Lima', 'utc_offset' => '-05:00', 'is_default' => true],
                
                // فیلیپین
                ['iso2' => 'PH', 'name' => 'Asia/Manila', 'utc_offset' => '+08:00', 'is_default' => true],
                
                // لهستان
                ['iso2' => 'PL', 'name' => 'Europe/Warsaw', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'PL', 'name' => 'Europe/Warsaw', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // پرتغال
                ['iso2' => 'PT', 'name' => 'Europe/Lisbon', 'utc_offset' => '+00:00', 'is_default' => true],
                ['iso2' => 'PT', 'name' => 'Europe/Lisbon', 'utc_offset' => '+01:00', 'is_default' => false],
                ['iso2' => 'PT', 'name' => 'Atlantic/Azores', 'utc_offset' => '-01:00', 'is_default' => false],
                
                // قطر
                ['iso2' => 'QA', 'name' => 'Asia/Qatar', 'utc_offset' => '+03:00', 'is_default' => true],
                
                // رومانی
                ['iso2' => 'RO', 'name' => 'Europe/Bucharest', 'utc_offset' => '+02:00', 'is_default' => true],
                ['iso2' => 'RO', 'name' => 'Europe/Bucharest', 'utc_offset' => '+03:00', 'is_default' => false],
                
                // روسیه
                ['iso2' => 'RU', 'name' => 'Europe/Moscow', 'utc_offset' => '+03:00', 'is_default' => true],
                ['iso2' => 'RU', 'name' => 'Asia/Yekaterinburg', 'utc_offset' => '+05:00', 'is_default' => false],
                ['iso2' => 'RU', 'name' => 'Asia/Novosibirsk', 'utc_offset' => '+07:00', 'is_default' => false],
                ['iso2' => 'RU', 'name' => 'Asia/Vladivostok', 'utc_offset' => '+10:00', 'is_default' => false],
                ['iso2' => 'RU', 'name' => 'Europe/Kaliningrad', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // عربستان سعودی
                ['iso2' => 'SA', 'name' => 'Asia/Riyadh', 'utc_offset' => '+03:00', 'is_default' => true],
                
                // سنگاپور
                ['iso2' => 'SG', 'name' => 'Asia/Singapore', 'utc_offset' => '+08:00', 'is_default' => true],
                
                // آفریقای جنوبی
                ['iso2' => 'ZA', 'name' => 'Africa/Johannesburg', 'utc_offset' => '+02:00', 'is_default' => true],
                
                // کره جنوبی
                ['iso2' => 'KR', 'name' => 'Asia/Seoul', 'utc_offset' => '+09:00', 'is_default' => true],
                
                // اسپانیا
                ['iso2' => 'ES', 'name' => 'Europe/Madrid', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'ES', 'name' => 'Europe/Madrid', 'utc_offset' => '+02:00', 'is_default' => false],
                ['iso2' => 'ES', 'name' => 'Atlantic/Canary', 'utc_offset' => '+00:00', 'is_default' => false],
                
                // سریلانکا
                ['iso2' => 'LK', 'name' => 'Asia/Colombo', 'utc_offset' => '+05:30', 'is_default' => true],
                
                // سوئد
                ['iso2' => 'SE', 'name' => 'Europe/Stockholm', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'SE', 'name' => 'Europe/Stockholm', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // سوئیس
                ['iso2' => 'CH', 'name' => 'Europe/Zurich', 'utc_offset' => '+01:00', 'is_default' => true],
                ['iso2' => 'CH', 'name' => 'Europe/Zurich', 'utc_offset' => '+02:00', 'is_default' => false],
                
                // سوریه
                ['iso2' => 'SY', 'name' => 'Asia/Damascus', 'utc_offset' => '+02:00', 'is_default' => true],
                ['iso2' => 'SY', 'name' => 'Asia/Damascus', 'utc_offset' => '+03:00', 'is_default' => false],
                
                // تایوان
                ['iso2' => 'TW', 'name' => 'Asia/Taipei', 'utc_offset' => '+08:00', 'is_default' => true],
                
                // تایلند
                ['iso2' => 'TH', 'name' => 'Asia/Bangkok', 'utc_offset' => '+07:00', 'is_default' => true],
                
                // ترکیه
                ['iso2' => 'TR', 'name' => 'Europe/Istanbul', 'utc_offset' => '+03:00', 'is_default' => true],
                
                // امارات متحده عربی
                ['iso2' => 'AE', 'name' => 'Asia/Dubai', 'utc_offset' => '+04:00', 'is_default' => true],
                
                // بریتانیا
                ['iso2' => 'GB', 'name' => 'Europe/London', 'utc_offset' => '+00:00', 'is_default' => true],
                ['iso2' => 'GB', 'name' => 'Europe/London', 'utc_offset' => '+01:00', 'is_default' => false],
                
                // آمریکا
                ['iso2' => 'US', 'name' => 'America/New_York', 'utc_offset' => '-05:00', 'is_default' => true],
                ['iso2' => 'US', 'name' => 'America/New_York', 'utc_offset' => '-04:00', 'is_default' => false],
                ['iso2' => 'US', 'name' => 'America/Chicago', 'utc_offset' => '-06:00', 'is_default' => false],
                ['iso2' => 'US', 'name' => 'America/Chicago', 'utc_offset' => '-05:00', 'is_default' => false],
                ['iso2' => 'US', 'name' => 'America/Denver', 'utc_offset' => '-07:00', 'is_default' => false],
                ['iso2' => 'US', 'name' => 'America/Denver', 'utc_offset' => '-06:00', 'is_default' => false],
                ['iso2' => 'US', 'name' => 'America/Los_Angeles', 'utc_offset' => '-08:00', 'is_default' => false],
                ['iso2' => 'US', 'name' => 'America/Los_Angeles', 'utc_offset' => '-07:00', 'is_default' => false],
                ['iso2' => 'US', 'name' => 'America/Anchorage', 'utc_offset' => '-09:00', 'is_default' => false],
                ['iso2' => 'US', 'name' => 'Pacific/Honolulu', 'utc_offset' => '-10:00', 'is_default' => false],
                
                // ازبکستان
                ['iso2' => 'UZ', 'name' => 'Asia/Tashkent', 'utc_offset' => '+05:00', 'is_default' => true],
                
                // ونزوئلا
                ['iso2' => 'VE', 'name' => 'America/Caracas', 'utc_offset' => '-04:00', 'is_default' => true],
                
                // ویتنام
                ['iso2' => 'VN', 'name' => 'Asia/Ho_Chi_Minh', 'utc_offset' => '+07:00', 'is_default' => true],
                
                // یمن
                ['iso2' => 'YE', 'name' => 'Asia/Aden', 'utc_offset' => '+03:00', 'is_default' => true],
                
                // زیمبابوه
                ['iso2' => 'ZW', 'name' => 'Africa/Harare', 'utc_offset' => '+02:00', 'is_default' => true],
            ];

            // شمارنده برای لاگ
            $insertedCount = 0;
            $skippedCount = 0;
            $skippedCountries = [];

            // تبدیل به فرمت قابل درج با country_id
            $timeZonesWithIds = array_map(function($timeZone) use ($countries, &$skippedCount, &$skippedCountries) {
                $country = $countries->get($timeZone['iso2']);
                
                if (!$country) {
                    $skippedCount++;
                    $skippedCountries[] = $timeZone['iso2'] . ' - ' . $timeZone['name'];
                    return null;
                }
                
                return [
                    'country_id' => $country->id,
                    'name' => $timeZone['name'],
                    'utc_offset' => $timeZone['utc_offset'],
                    'is_default' => $timeZone['is_default'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $timeZones);

            // حذف رکوردهای null
            $timeZonesWithIds = array_filter($timeZonesWithIds);

            // درج داده‌ها با تکه‌تکه کردن
            $chunkSize = 50;
            $chunks = array_chunk($timeZonesWithIds, $chunkSize);
            
            foreach ($chunks as $chunk) {
                if (!empty($chunk)) {
                    try {
                        DB::table('timezones')->insert($chunk);
                        $insertedCount += count($chunk);
                    } catch (Exception $e) {
                        // لاگ خطا و ادامه
                        Log::error('Error inserting time zone chunk: ' . $e->getMessage());
                        
                        // اگر خطا مربوط به تکراری بودن بود، یکی یکی درج کن
                        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                            foreach ($chunk as $record) {
                                try {
                                    DB::table('timezones')->insert($record);
                                    $insertedCount++;
                                } catch (Exception $innerE) {
                                    Log::warning('Skipped duplicate time zone: ' . $record['name']);
                                    $skippedCount++;
                                }
                            }
                        } else {
                            // اگر خطای دیگری بود، rollback کن
                            throw $e;
                        }
                    }
                }
            }

            // لاگ نهایی
            Log::info("TimeZone Seeder completed: Inserted {$insertedCount} records, Skipped {$skippedCount} records");
            
            if (!empty($skippedCountries)) {
                Log::warning("Skipped countries: " . implode(', ', array_unique($skippedCountries)));
            }

            // commit تراکنش
            DB::commit();

            $this->command->info("✅ Time zones seeded successfully!");
            $this->command->info("   📊 Inserted: {$insertedCount} records");
            $this->command->info("   ⏭️  Skipped: {$skippedCount} records");
            
            if (!empty($skippedCountries)) {
                $this->command->warn("   ⚠️  Skipped countries: " . implode(', ', array_unique($skippedCountries)));
            }

        } catch (Exception $e) {
            // در صورت خطا، rollback
            DB::rollBack();
            
            // لاگ خطا
            Log::error("TimeZone Seeder failed: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            $this->command->error("❌ Seeding failed: " . $e->getMessage());
            
            throw $e;
        }
    }
}