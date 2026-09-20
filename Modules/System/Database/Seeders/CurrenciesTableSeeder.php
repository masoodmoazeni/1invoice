<?php

namespace Modules\System\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesTableSeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            ['code'=>'IRR','name'=>'Iranian Rial','symbol'=>'﷼','decimal_places'=>0,'rounding'=>1],
            ['code'=>'USD','name'=>'US Dollar','symbol'=>'$','decimal_places'=>2,'rounding'=>0.01],
            ['code'=>'EUR','name'=>'Euro','symbol'=>'€','decimal_places'=>2,'rounding'=>0.01],
            ['code'=>'GBP','name'=>'British Pound','symbol'=>'£','decimal_places'=>2,'rounding'=>0.01],
            ['code'=>'AED','name'=>'UAE Dirham','symbol'=>'د.إ','decimal_places'=>2,'rounding'=>0.01],
            ['code'=>'TRY','name'=>'Turkish Lira','symbol'=>'₺','decimal_places'=>2,'rounding'=>0.01],
            ['code'=>'CAD','name'=>'Canadian Dollar','symbol'=>'CA$','decimal_places'=>2,'rounding'=>0.01],
            ['code'=>'AUD','name'=>'Australian Dollar','symbol'=>'A$','decimal_places'=>2,'rounding'=>0.01],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->updateOrInsert(
                ['code' => $currency['code']],
                array_merge($currency, ['is_active'=>true, 'updated_at'=>now(), 'created_at'=>now()])
            );
        }
    }
}
