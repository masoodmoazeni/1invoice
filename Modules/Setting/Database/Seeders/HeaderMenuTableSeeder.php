<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Entities\HeaderMenuItem;

class HeaderMenuTableSeeder extends Seeder
{
    public function run(): void
    {
        if (HeaderMenuItem::query()->exists()) {
            return;
        }

        $menu = [
            [
                'label' => 'Home',
                'href' => '/',
                'sort_order' => 1,
            ],
            [
                'label' => 'For Sale',
                'href' => '/beauty-industry-businesses-for-sale',
                'sort_order' => 2,
            ],
            [
                'label' => 'Buyers & Sellers',
                'is_dropdown' => true,
                'cols' => 3,
                'sort_order' => 3,
                'items' => [
                    ['label' => 'Sellers: Start Here', 'href' => '/sellers-start-here', 'description' => 'Take the first step in selling'],
                    ['label' => 'Ready to: Sell Your Business', 'href' => '/ready-to-sell', 'description' => 'List your business with confidence'],
                    ['label' => 'Chat with a broker', 'href' => '/chat-with-a-business-broker', 'description' => 'Experienced guidance for internal sales'],
                    ['label' => 'Buyers: Start Here', 'href' => '/buyers-start-here', 'description' => 'Get expert buying guidance'],
                    ['label' => 'Prepare to: Sell Your Business', 'href' => '/prepare-to-sell-your-beauty-business', 'description' => 'Get your business sale ready'],
                    ['label' => 'Employee Buyouts', 'href' => '/salon-employee-buyout-mediation-services', 'description' => 'Experienced guidance for internal sales'],
                    ['label' => 'For Sale: Beauty Businesses Listings', 'href' => '/beauty-industry-businesses-for-sale', 'description' => 'Find the perfect beauty industry business'],
                    ['label' => 'Free Business Valuation Tool', 'href' => '/free-salon-business-valuation', 'description' => 'Find the value of your business'],
                    ['label' => '8 Mistakes When Selling a Salon', 'href' => '/8-mistakes-when-selling-a-salon-how-to-avoid-disaster-when-selling-a-beauty-industry-business', 'description' => 'Avoid costly missteps before selling'],
                ],
            ],
            [
                'label' => 'About Us',
                'is_dropdown' => true,
                'cols' => 2,
                'sort_order' => 4,
                'items' => [
                    ['label' => 'Our Mission: Empowering Beauty Business', 'href' => '/our-mission', 'description' => 'Guidance, clarity, and confidence'],
                    ['label' => 'Our Founder: Susan Wos', 'href' => '/salon-industry-connector-susan-wos', 'description' => 'Industry expert and lead Broker'],
                    ['label' => 'Meet our Broker Team', 'href' => '/salonspa-connection-business-brokers', 'description' => 'Real people. Real beauty experience.'],
                    ['label' => 'Media & Podcasts', 'href' => '/salonspa-connection-media', 'description' => 'SSC stories, strategy, and insights'],
                    ['label' => 'Contact Us', 'href' => '/contact-us', 'description' => 'Let\'s talk through it.'],
                    ['label' => 'Careers with SSC', 'href' => '/careers-for-former-salon-owners', 'description' => 'Join our growing team'],
                ],
            ],
            [
                'label' => 'Resources',
                'is_dropdown' => true,
                'cols' => 2,
                'sort_order' => 5,
                'items' => [
                    ['label' => 'Industry Mentors, Coaches, Consultants', 'href' => '/salon-consultants', 'description' => 'Expertise for all aspects of business'],
                    ['label' => 'Everything Beauty Industry: Blog', 'href' => '/blogs', 'description' => 'Articles, insights, and resources'],
                    ['label' => 'Frequently Asked Questions', 'href' => '/faq', 'description' => 'Answers about selling and buying beauty businesses'],
                    ['label' => 'Booth Rental Calculator', 'href' => '/booth-rental-prices-for-a-salon-calculate-the-cost', 'description' => 'Is booth renting right for you?'],
                    ['label' => 'Data on the Beauty Industry', 'href' => '/salon-industry-statistics', 'description' => 'Salon Industry Statistics'],
                ],
            ],
        ];

        foreach ($menu as $entry) {
            $children = $entry['items'] ?? [];
            unset($entry['items']);

            $parent = HeaderMenuItem::create([
                ...$entry,
                'status' => HeaderMenuItem::STATUS_PUBLISHED,
                'link_type' => HeaderMenuItem::LINK_TYPE_CUSTOM,
            ]);

            foreach ($children as $index => $child) {
                HeaderMenuItem::create([
                    'parent_id' => $parent->id,
                    'label' => $child['label'],
                    'href' => $child['href'],
                    'description' => $child['description'] ?? null,
                    'sort_order' => $index + 1,
                    'status' => HeaderMenuItem::STATUS_PUBLISHED,
                    'link_type' => HeaderMenuItem::LINK_TYPE_CUSTOM,
                    'is_dropdown' => false,
                ]);
            }
        }
    }
}
