<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Entities\State;
use Faker\Factory as Faker;

class StateTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        /*
        |--------------------------------------------------------------------------
        | Canada – country_id = 39
        |--------------------------------------------------------------------------
        */
        $canadaStates = [
            ['id' => 872, 'name' => 'Alberta', 'code' => 'AB'],
            ['id' => 875, 'name' => 'British Columbia', 'code' => 'BC'],
            ['id' => 867, 'name' => 'Manitoba', 'code' => 'MB'],
            ['id' => 868, 'name' => 'New Brunswick', 'code' => 'NB'],
            ['id' => 877, 'name' => 'Newfoundland and Labrador', 'code' => 'NL'],
            ['id' => 874, 'name' => 'Nova Scotia', 'code' => 'NS'],
            ['id' => 866, 'name' => 'Ontario', 'code' => 'ON'],
            ['id' => 871, 'name' => 'Prince Edward Island', 'code' => 'PE'],
            ['id' => 873, 'name' => 'Quebec', 'code' => 'QC'],
            ['id' => 870, 'name' => 'Saskatchewan', 'code' => 'SK'],
        ];

        foreach ($canadaStates as $s) {
            State::updateOrCreate(
                ['id' => $s['id']],
                [
                    'country_id' => 39,
                    'state_code' => $s['code'],
                    'name' => $s['name'],
                    'disclaimer' => "Province of {$s['name']}: " . $faker->paragraphs(2, true),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | USA – country_id = 233
        |--------------------------------------------------------------------------
        */
        $usaStates = [
            ['id' => 1399, 'name' => 'Delaware', 'code' => 'DE'],
            ['id' => 1400, 'name' => 'Alaska', 'code' => 'AK'],
            ['id' => 1401, 'name' => 'Maryland', 'code' => 'MD'],
            ['id' => 1404, 'name' => 'New Hampshire', 'code' => 'NH'],
            ['id' => 1406, 'name' => 'Kansas', 'code' => 'KS'],
            ['id' => 1407, 'name' => 'Texas', 'code' => 'TX'],
            ['id' => 1408, 'name' => 'Nebraska', 'code' => 'NE'],
            ['id' => 1409, 'name' => 'Vermont', 'code' => 'VT'],
            ['id' => 1411, 'name' => 'Hawaii', 'code' => 'HI'],
            ['id' => 1414, 'name' => 'Utah', 'code' => 'UT'],
            ['id' => 1415, 'name' => 'Oregon', 'code' => 'OR'],
            ['id' => 1416, 'name' => 'California', 'code' => 'CA'],
            ['id' => 1417, 'name' => 'New Jersey', 'code' => 'NJ'],
            ['id' => 1418, 'name' => 'North Dakota', 'code' => 'ND'],
            ['id' => 1419, 'name' => 'Kentucky', 'code' => 'KY'],
            ['id' => 1420, 'name' => 'Minnesota', 'code' => 'MN'],
            ['id' => 1421, 'name' => 'Oklahoma', 'code' => 'OK'],
            ['id' => 1422, 'name' => 'Pennsylvania', 'code' => 'PA'],
            ['id' => 1423, 'name' => 'New Mexico', 'code' => 'NM'],
            ['id' => 1425, 'name' => 'Illinois', 'code' => 'IL'],
            ['id' => 1426, 'name' => 'Michigan', 'code' => 'MI'],
            ['id' => 1427, 'name' => 'Virginia', 'code' => 'VA'],
            ['id' => 1429, 'name' => 'West Virginia', 'code' => 'WV'],
            ['id' => 1430, 'name' => 'Mississippi', 'code' => 'MS'],
            ['id' => 1433, 'name' => 'Massachusetts', 'code' => 'MA'],
            ['id' => 1434, 'name' => 'Arizona', 'code' => 'AZ'],
            ['id' => 1435, 'name' => 'Connecticut', 'code' => 'CT'],
            ['id' => 1436, 'name' => 'Florida', 'code' => 'FL'],
            ['id' => 1440, 'name' => 'Indiana', 'code' => 'IN'],
            ['id' => 1441, 'name' => 'Wisconsin', 'code' => 'WI'],
            ['id' => 1442, 'name' => 'Wyoming', 'code' => 'WY'],
            ['id' => 1443, 'name' => 'South Carolina', 'code' => 'SC'],
            ['id' => 1444, 'name' => 'Arkansas', 'code' => 'AR'],
            ['id' => 1445, 'name' => 'South Dakota', 'code' => 'SD'],
            ['id' => 1446, 'name' => 'Montana', 'code' => 'MT'],
            ['id' => 1447, 'name' => 'North Carolina', 'code' => 'NC'],
            ['id' => 1450, 'name' => 'Colorado', 'code' => 'CO'],
            ['id' => 1451, 'name' => 'Missouri', 'code' => 'MO'],
            ['id' => 1452, 'name' => 'New York', 'code' => 'NY'],
            ['id' => 1453, 'name' => 'Maine', 'code' => 'ME'],
            ['id' => 1454, 'name' => 'Tennessee', 'code' => 'TN'],
            ['id' => 1455, 'name' => 'Georgia', 'code' => 'GA'],
            ['id' => 1456, 'name' => 'Alabama', 'code' => 'AL'],
            ['id' => 1457, 'name' => 'Louisiana', 'code' => 'LA'],
            ['id' => 1458, 'name' => 'Nevada', 'code' => 'NV'],
            ['id' => 1459, 'name' => 'Iowa', 'code' => 'IA'],
            ['id' => 1460, 'name' => 'Idaho', 'code' => 'ID'],
            ['id' => 1461, 'name' => 'Rhode Island', 'code' => 'RI'],
            ['id' => 1462, 'name' => 'Washington', 'code' => 'WA'],
            ['id' => 4851, 'name' => 'Ohio', 'code' => 'OH'],
        ];

        foreach ($usaStates as $s) {
            State::updateOrCreate(
                ['id' => $s['id']],
                [
                    'country_id' => 233,
                    'state_code' => $s['code'],
                    'name'       => $s['name'],
                    'disclaimer' => "State of {$s['name']}: " . $faker->paragraphs(2, true),
                ]
            );
        }
    }
}
