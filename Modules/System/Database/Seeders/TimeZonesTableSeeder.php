<?php

namespace Modules\System\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimeZonesTableSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['country'=>'IR','name'=>'Asia/Tehran','utc_offset'=>'+03:30','is_default'=>true],
            ['country'=>'US','name'=>'America/New_York','utc_offset'=>'-05:00','is_default'=>true],
            ['country'=>'GB','name'=>'Europe/London','utc_offset'=>'+00:00','is_default'=>true],
            ['country'=>'DE','name'=>'Europe/Berlin','utc_offset'=>'+01:00','is_default'=>true],
            ['country'=>'AE','name'=>'Asia/Dubai','utc_offset'=>'+04:00','is_default'=>true],
            ['country'=>'TR','name'=>'Europe/Istanbul','utc_offset'=>'+03:00','is_default'=>true],
            ['country'=>'CA','name'=>'America/Toronto','utc_offset'=>'-05:00','is_default'=>true],
            ['country'=>'AU','name'=>'Australia/Sydney','utc_offset'=>'+10:00','is_default'=>true],
        ];

        foreach ($zones as $zone) {
            $countryId = DB::table('countries')->where('iso2', $zone['country'])->value('id');
            if (!$countryId) continue;

            DB::table('timezones')->updateOrInsert(
                ['name' => $zone['name']],
                ['country_id'=>$countryId, 'utc_offset'=>$zone['utc_offset'], 'is_default'=>$zone['is_default'], 'updated_at'=>now(), 'created_at'=>now()]
            );
        }
    }
}
