<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SaleAdvantageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $sales = [
            'Turnkey & Profitable',
            'Growth Opportunity',
            'Equipment & Buildout',
        ];

        $occupancy = [
            'Lease',
            'Real State Included'
        ];

        $business = [
            'Independent Business',
            'Franchise',
        ];

        // Insert sales
        foreach ($sales as $item) {
            DB::table('sale_advantages')->insert([
                'type'        => 'sale',
                'title'       => $item,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        foreach ($occupancy as $item) {
            DB::table('sale_advantages')->insert([
                'type'        => 'occupancy',
                'title'       => $item,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        foreach ($business as $item) {
            DB::table('sale_advantages')->insert([
                'type'        => 'business',
                'title'       => $item,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}
