<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->truncate();

        $countries = [
            ['iso2' => 'AF', 'iso3' => 'AFG', 'name' => 'Afghanistan', 'numeric_code' => '004', 'phone_code' => '+93', 'capital' => 'Kabul'],
            ['iso2' => 'AL', 'iso3' => 'ALB', 'name' => 'Albania', 'numeric_code' => '008', 'phone_code' => '+355', 'capital' => 'Tirana'],
            ['iso2' => 'DZ', 'iso3' => 'DZA', 'name' => 'Algeria', 'numeric_code' => '012', 'phone_code' => '+213', 'capital' => 'Algiers'],
            ['iso2' => 'UY', 'iso3' => 'URY', 'name' => 'Uruguay', 'numeric_code' => '858', 'phone_code' => '+598', 'capital' => 'Montevideo'],
            ['iso2' => 'UZ', 'iso3' => 'UZB', 'name' => 'Uzbekistan', 'numeric_code' => '860', 'phone_code' => '+998', 'capital' => 'Tashkent'],
            ['iso2' => 'VU', 'iso3' => 'VUT', 'name' => 'Vanuatu', 'numeric_code' => '548', 'phone_code' => '+678', 'capital' => 'Port Vila'],
            ['iso2' => 'VA', 'iso3' => 'VAT', 'name' => 'Vatican City', 'numeric_code' => '336', 'phone_code' => '+379', 'capital' => 'Vatican City'],
            ['iso2' => 'VE', 'iso3' => 'VEN', 'name' => 'Venezuela', 'numeric_code' => '862', 'phone_code' => '+58', 'capital' => 'Caracas'],
            ['iso2' => 'VN', 'iso3' => 'VNM', 'name' => 'Vietnam', 'numeric_code' => '704', 'phone_code' => '+84', 'capital' => 'Hanoi'],
            ['iso2' => 'YE', 'iso3' => 'YEM', 'name' => 'Yemen', 'numeric_code' => '887', 'phone_code' => '+967', 'capital' => 'Sana\'a'],
            ['iso2' => 'ZM', 'iso3' => 'ZMB', 'name' => 'Zambia', 'numeric_code' => '894', 'phone_code' => '+260', 'capital' => 'Lusaka'],
            ['iso2' => 'ZW', 'iso3' => 'ZWE', 'name' => 'Zimbabwe', 'numeric_code' => '716', 'phone_code' => '+263', 'capital' => 'Harare'],
        ];

        // تبدیل آرایه به فرمت قابل درج در دیتابیس
        $countriesWithTimestamps = array_map(function($country) {
            return array_merge($country, [
                'created_at' => now(),
                'updated_at' => now(),
                'is_active' => true,
            ]);
        }, $countries);

        // درج داده‌ها با تکه‌تکه کردن برای جلوگیری از خطا
        $chunkSize = 50;
        $chunks = array_chunk($countriesWithTimestamps, $chunkSize);
        
        foreach ($chunks as $chunk) {
            DB::table('countries')->insert($chunk);
        }
    }
}
