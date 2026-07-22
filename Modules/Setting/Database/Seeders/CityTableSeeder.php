<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Setting\Entities\City;

class CityTableSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = public_path('settings/city.xlsx');

        if (!file_exists($filePath)) {
            dd("❌ city.xlsx file not found in: " . $filePath);
        }

        // خواندن اکسل به صورت Collection (کم‌مصرف)
        Excel::import(new class implements \Maatwebsite\Excel\Concerns\ToCollection {

            public function collection(\Illuminate\Support\Collection $rows)
            {
                // حذف هدر
                $rows->shift();

                // chunk واقعی
                $rows->chunk(500)->each(function ($chunk) {

                    $insertData = [];

                    foreach ($chunk as $row) {

                        $name      = $row[0] ?? null;
                        $stateId   = $row[1] ?? null;
                        $countryId = $row[2] ?? null;

                        if (!$name || !$stateId || !$countryId) {
                            continue;
                        }

                        $insertData[] = [
                            'name'        => $name,
                            'state_id'    => $stateId,
                            'country_id'  => $countryId,
                            'description' => $name,
                            'status'      => true,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ];
                    }

                    if (!empty($insertData)) {
                        City::insertOrIgnore($insertData);
                    }
                });
            }

        }, $filePath);

        echo "✅ City seeding completed successfully!";
    }
}
