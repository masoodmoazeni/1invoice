<?php

namespace Modules\System\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExchangeRatesTableSeeder extends Seeder
{
    public function run(): void
    {
        $rates = [
            ['from'=>'USD','to'=>'IRR','rate'=>42000,'date'=>'2026-01-01','source'=>'manual'],
            ['from'=>'EUR','to'=>'USD','rate'=>1.08,'date'=>'2026-01-01','source'=>'api'],
            ['from'=>'GBP','to'=>'USD','rate'=>1.27,'date'=>'2026-01-01','source'=>'api'],
            ['from'=>'AED','to'=>'USD','rate'=>0.272294,'date'=>'2026-01-01','source'=>'central_bank'],
            ['from'=>'TRY','to'=>'USD','rate'=>0.031,'date'=>'2026-01-01','source'=>'exchange_market'],
            ['from'=>'CAD','to'=>'USD','rate'=>0.735,'date'=>'2026-01-01','source'=>'api'],
            ['from'=>'AUD','to'=>'USD','rate'=>0.625,'date'=>'2026-01-01','source'=>'api'],
        ];

        foreach ($rates as $rate) {
            $fromId = DB::table('currencies')->where('code', $rate['from'])->value('id');
            $toId = DB::table('currencies')->where('code', $rate['to'])->value('id');
            if (!$fromId || !$toId) continue;

            DB::table('exchange_rates')->updateOrInsert(
                ['from_currency_id'=>$fromId, 'to_currency_id'=>$toId, 'effective_date'=>$rate['date']],
                ['rate'=>$rate['rate'], 'source'=>$rate['source'], 'updated_at'=>now(), 'created_at'=>now()]
            );
        }
    }
}
