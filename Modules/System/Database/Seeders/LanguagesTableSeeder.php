<?php

namespace Modules\System\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['code'=>'fa','name'=>'Persian','native_name'=>'فارسی','direction'=>'rtl'],
            ['code'=>'en','name'=>'English','native_name'=>'English','direction'=>'ltr'],
            ['code'=>'ar','name'=>'Arabic','native_name'=>'العربية','direction'=>'rtl'],
            ['code'=>'de','name'=>'German','native_name'=>'Deutsch','direction'=>'ltr'],
            ['code'=>'tr','name'=>'Turkish','native_name'=>'Türkçe','direction'=>'ltr'],
            ['code'=>'fr','name'=>'French','native_name'=>'Français','direction'=>'ltr'],
        ];

        foreach ($languages as $language) {
            DB::table('languages')->updateOrInsert(
                ['code' => $language['code']],
                array_merge($language, ['is_active'=>true, 'updated_at'=>now(), 'created_at'=>now()])
            );
        }
    }
}
