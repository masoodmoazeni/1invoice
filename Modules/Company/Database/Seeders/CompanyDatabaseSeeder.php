<?php

namespace Modules\Company\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CompanyDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();
        $this->call([CompanySeeder::class]);
    }
}
