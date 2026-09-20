<?php

namespace Modules\System\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['iso2'=>'IR','iso3'=>'IRN','name'=>'Iran','numeric_code'=>'364','phone_code'=>'+98','capital'=>'Tehran'],
            ['iso2'=>'US','iso3'=>'USA','name'=>'United States','numeric_code'=>'840','phone_code'=>'+1','capital'=>'Washington, D.C.'],
            ['iso2'=>'GB','iso3'=>'GBR','name'=>'United Kingdom','numeric_code'=>'826','phone_code'=>'+44','capital'=>'London'],
            ['iso2'=>'DE','iso3'=>'DEU','name'=>'Germany','numeric_code'=>'276','phone_code'=>'+49','capital'=>'Berlin'],
            ['iso2'=>'AE','iso3'=>'ARE','name'=>'United Arab Emirates','numeric_code'=>'784','phone_code'=>'+971','capital'=>'Abu Dhabi'],
            ['iso2'=>'TR','iso3'=>'TUR','name'=>'Türkiye','numeric_code'=>'792','phone_code'=>'+90','capital'=>'Ankara'],
            ['iso2'=>'CA','iso3'=>'CAN','name'=>'Canada','numeric_code'=>'124','phone_code'=>'+1','capital'=>'Ottawa'],
            ['iso2'=>'AU','iso3'=>'AUS','name'=>'Australia','numeric_code'=>'036','phone_code'=>'+61','capital'=>'Canberra'],
        ];

        foreach ($countries as $country) {
            DB::table('countries')->updateOrInsert(
                ['iso2' => $country['iso2']],
                array_merge($country, ['is_active'=>true, 'updated_at'=>now(), 'created_at'=>now()])
            );
        }
    }
}
