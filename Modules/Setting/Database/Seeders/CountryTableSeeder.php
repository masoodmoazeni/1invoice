<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Entities\Country;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run():void
    {
        Country::insert([
            ['id' => 39,'country_code' => 'CA', 'name' => 'Canada', 'description' => 'Canada', 'flag' => null, 'status' => true],
            ['id' => 233,'country_code' => 'US', 'name' => 'United States', 'description' => 'United States', 'flag' => null, 'status' => true],
        ]);
    }
}
