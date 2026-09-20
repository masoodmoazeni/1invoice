<?php

namespace Modules\System\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SystemDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        $this->call([
            CountriesTableSeeder::class,
            CurrenciesTableSeeder::class,
            LanguagesTableSeeder::class,
            TimeZonesTableSeeder::class,
            ExchangeRatesTableSeeder::class,
        ]);
    }
}
