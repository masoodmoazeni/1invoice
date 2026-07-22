<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Entities\Category;

class CategorySubcategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $categories = [
            'Salon' => ['Hair Salon', 'Nail Salon', 'Full Service Salon', 'Blowdry Bar', 'Salon Suite', 'Ethnic Salon', "Children's Salon", 'Luxury Salon', 'Eco-Friendly Salon', 'Spray Tanning Salon', 'Hair Loss Service Salon'],
            'Spa' => ['Day Spa', 'Medical Spa', 'Permanent Makeup', 'Makeup', 'Massage', 'Hair Removal- Waxing or Sugaring', 'Lash Business', 'Laser Spa', 'Skin Clinic', 'Facial Spa', 'Head Spa', 'Anti-Aging Skincare Service'],
            'Barber shop' => ['Mens Grooming Lounge', 'Traditional Barber Shop', 'Barber Salon', 'Ethnic Barber Shop', 'Eco-Friendly Barber Shop'],
            'Other Beauty Business' => ['School', 'Online Beauty Business', 'Product Company', 'Bridal', 'Mobile']
        ];

        foreach ($categories as $parent => $subs) {
            $parentCategory = Category::create([
                'parent_id'   => null,
                'title'       => $parent,
                'description' => ucfirst($parent) . ' main category',
                'status'      => 1,
            ]);

            foreach ($subs as $sub) {
                Category::create([
                    'parent_id'   => $parentCategory->id,
                    'title'       => $sub,
                    'description' => $sub . ' under ' . $parent,
                    'status'      => 1,
                ]);
            }
        }
    }
}
