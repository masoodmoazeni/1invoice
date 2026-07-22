<?php

namespace Modules\Setting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Setting\Entities\FaqCategory;
use Modules\Setting\Entities\FaqItem;

class FaqTableSeeder extends Seeder
{
    public function run(): void
    {
        if (FaqCategory::query()->exists()) {
            return;
        }

        $categories = [
            [
                'title' => 'Questions About SalonSpa Connection',
                'slug' => 'about-salonspa-connection',
                'sort_order' => 0,
                'items' => [
                    [
                        'question' => 'Is SalonSpa Connection a business brokerage specializing in Salon sales?',
                        'answer' => '<p>Yes! SalonSpa Connection is a specialized Business Brokerage, specifically for salons &amp; the beauty industry. We provide high-quality and flexible brokerage services for salon, spa, and barber industry owners, from brick-and-mortar businesses to schools to online businesses. We cover the entire beauty industry.</p>',
                    ],
                    [
                        'question' => 'Does SalonSpa Connection offer professional assistance for selling a small beauty business?',
                        'answer' => '<p>Yes, SalonSpa Connection provides professional assistance to a wide variety of beauty industry businesses, from small salons and suites to large, multi-million dollar salons. Whether you just need advice on how to sell your salon or want a broker in your corner throughout the process, our expertise in selling beauty businesses is unmatched.</p>',
                    ],
                    [
                        'question' => 'Are you virtual brokers or agents specializing in salon sales?',
                        'answer' => '<div class="space-y-4"><p>Yes and no. SalonSpa Connection is a team of real beauty industry experts who are trained to sell beauty businesses. While some of our brokers are in person, some brokers offer virtual services in selling salons.</p><p>The term &ldquo;agent&rdquo; is primarily associated with real estate. Depending on the state, the license may designate the role as &lsquo;salesperson&rsquo; rather than &lsquo;agent&rsquo; — both refer to the same function. Real estate agents typically do not understand how to sell a business, nor do they have education in business brokering.</p><p>The term &ldquo;broker&rdquo; means something different in real estate vs. business brokerage. A real estate broker is a person with several years of experience and education in real estate who undergoes rigorous training and meets state licensure requirements in order to have real estate agents working for them in a real estate brokerage.</p><p>A &ldquo;business broker&rdquo; describes a person who has specialized training in order to assist business owners successfully selling their business. Business brokers often are also licensed real estate agents, and some business brokers are also industry experts or &ldquo;niche&rdquo; brokers, selling in one industry. SalonSpa Connection offers brokers who are sometimes virtual, specializing in salon sales and a wide variety of beauty industry businesses.</p><p><a href="/chat-with-a-business-broker">Click here to find a business broker specializing in salon sales.</a></p><p><a href="https://www.ibba.org" target="_blank" rel="noopener noreferrer">Click here to visit the IBBA website to find other industry specialists and generalist business brokers.</a></p></div>',
                    ],
                    [
                        'question' => 'Can I find brokers who handle salon business sales in my state?',
                        'answer' => '<p>Yes, we can assist in connecting you with brokers who handle business sales in your state. We have an excellent team of business brokers who are also beauty industry experts who can guide you through selling your business from start to finish. <a href="/contact-us">Request a consultation here</a> if you would like to discuss selling your salon, today!</p>',
                    ],
                    [
                        'question' => 'Do you provide a list of consultants specializing in salon business exit strategies?',
                        'answer' => '<p>SalonSpa Connection is home to several consultants specializing in salon business exit strategies! We have an on-staff CEPA-trained exit planner, and we also recommend starting your exit strategy long before you sell to maximize your sale price. If you are 3 years or less from selling your salon, <a href="/contact-us">contact SalonSpa Connection for assistance</a>. We also recommend <a href="https://qnity.com" target="_blank" rel="noopener noreferrer">Qnity</a> as the top consultants in the beauty industry to assist with exit planning if you are more than 3 years away from selling your business.</p>',
                    ],
                    [
                        'question' => 'Does your company offer confidential salon sale listings?',
                        'answer' => '<p>Yes! SalonSpa Connection offers confidential listings for owners who want to sell their salons, spas, and barbershop businesses. The level of confidentiality in your sale is at the seller&apos;s discretion. 75% of our sales are highly confidential, and we require buyers to sign a non-disclosure agreement (NDA) prior to learning more about confidential salon sales. Working with a business broker at SalonSpa Connection provides the level of confidentiality needed to sell a beauty business privately. For private &amp; confidential sales, we market your business with a general description — no name, no address, no identifying details until a buyer signs an NDA and is qualified. Confidentiality is standard practice and something we take seriously.</p>',
                    ],
                ],
            ],
            [
                'title' => 'Questions About Selling Salon, Spa & Barber Businesses',
                'slug' => 'selling-salon-spa-barber',
                'sort_order' => 1,
                'items' => [
                    [
                        'question' => 'Should I hire a broker to sell my salon?',
                        'answer' => '<p>Making the choice to hire a broker to sell your salon comes down to how much of the sale process you&apos;re equipped to manage on your own. Ask yourself if you can manage finding the right buyer, confidentiality/NDAs, fair market valuation, letters of intent, and due diligence. If you answered no to any of these questions, it may be a good idea to hire a broker to sell your salon or explore à la carte brokerage services such as SalonSpa Connection&apos;s Broker Lite services.</p>',
                    ],
                    [
                        'question' => 'Is SalonSpa Connection a company that offers salon business valuation services?',
                        'answer' => '<p>Yes, SalonSpa Connection offers professional salon business valuation services as well as a <a href="/articles/free-salon-business-valuation">free, online tool to assess the value of your salon business</a>. Valuations are important for selling a business to a buyer, selling to a key employee, assisting in a partner exit or buyout, divorce settlements, and more. If you would like to discuss getting a professional salon business valuation, <a href="/contact-us">contact us today</a> to see how we can serve you!</p>',
                    ],
                    [
                        'question' => 'What marketing strategies work best to sell a salon fast?',
                        'answer' => '<p>Needing to sell a salon fast requires access to a database of active buyers, connections to the right salon industry experts, and exposure online through social media and search engine-optimized platforms. SalonSpa Connection provides maximum exposure and strategies for beauty industry business owners who want to sell their salon businesses fast. <a href="/contact-us">Reach out to us</a> if you need exposure or guidance in selling a salon.</p>',
                    ],
                    [
                        'question' => 'What online marketplaces offer the highest exposure for selling a salon and spa?',
                        'answer' => '<p>SalonSpa Connection provides the highest exposure to beauty industry buyers available. Our vast network of owners, professionals, consultants, and salespeople covers the boots-on-the-ground buyer recruitment efforts. Posting your salon, spa or barber business on our website and social media yields local visibility to over 50,000 people in your local area in less than 6 weeks. <a href="/contact-us">Contact us today</a> to discuss how we can help you.</p>',
                    ],
                    [
                        'question' => 'What do potential buyers look for in a salon acquisition?',
                        'answer' => '<p>Potential buyers look for different things depending on where they are in their journey as a business owner. Buyer Bucket #1 includes buyers seeking turnkey, furnished and built-out spaces. Buyer Bucket #2 includes buyers seeking profitable businesses to acquire. Understanding which type of buyer your business attracts affects how it&apos;s priced and marketed. <a href="/contact-us">Contact SalonSpa Connection</a> to discuss your buyer pool.</p>',
                    ],
                    [
                        'question' => 'What is the average commission rate for business brokers selling salons?',
                        'answer' => '<p>Commission rates vary between 8–15% nationally, with Mainstreet salon businesses paying a higher rate versus Lower Middle Market salons. SalonSpa Connection charges between 9–11% for full broker services for Mainstreet beauty businesses. For Lower to Middle Market beauty businesses, we offer a monthly retainer option for between a 7–8% commission rate or 8–9% with no monthly retainer. SalonSpa Connection also offers Broker Lite for sellers who want à la carte services. <a href="/chat-with-a-business-broker">Chat with an expert who sells salons</a> for guidance on commissions and services.</p>',
                    ],
                    [
                        'question' => 'What are common fees associated with selling a salon through a broker?',
                        'answer' => '<p>Common fees associated with selling a salon through a broker include success commissions, listing fees, deposits, marketing fees, monthly retainers, and flat fee broker services. <a href="/chat-with-a-business-broker">Explore SalonSpa Connection&apos;s flexible and fair brokerage fees</a> by chatting with a salon broker.</p>',
                    ],
                    [
                        'question' => 'Where can I find recent sales data to compare salon sale prices?',
                        'answer' => '<p>SalonSpa Connection houses the most comprehensive and recent sales data to compare salon sale prices ranging from $50,000 up to $20,000,000. On the <a href="/articles/salon-industry-statistics">Beauty Business Sale Statistics page</a>, you will find public-facing data on the sale of salon businesses. <a href="/chat-with-a-business-broker">Chat with a salon business broker</a> for more granular information on what your beauty business will sell for.</p>',
                    ],
                    [
                        'question' => 'How long does it take to sell a salon?',
                        'answer' => '<p>The average time it takes for a salon to sell when it is priced fairly is <strong>6 months to one year</strong>. Factors that can shorten the timeline include fair pricing, clean &amp; organized financials, a favorable lease and an easy-to-work-with landlord, and seller financing availability. Working with a broker who has an active salon buyer database can significantly reduce time on market.</p>',
                    ],
                    [
                        'question' => 'How do I find investors interested in buying my salon?',
                        'answer' => '<p>We work with both strategic and financial buyers. Investors typically look for profitable businesses they can operate remotely, buy to replace their income, roll up into a larger salon group, or to start a new career. <a href="/contact-us">Contact SalonSpa Connection</a> to discuss our list of investors interested in buying salons.</p>',
                    ],
                    [
                        'question' => 'Are there financing companies that offer loans specifically for purchasing salons?',
                        'answer' => '<p>No, there are no companies that offer loans specifically for purchasing salons. Companies offering business acquisition loans are traditionally banks and lenders that offer government-backed SBA loans. Alternative lenders such as SoFi or salon software companies such as GlossGenius lend to users of their software for small business loans or cash advances. Seller financing is also common in the sale of salons and across most industries.</p>',
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $items = $categoryData['items'];
            unset($categoryData['items']);

            $category = FaqCategory::create([
                ...$categoryData,
                'status' => FaqCategory::STATUS_PUBLISHED,
            ]);

            foreach ($items as $index => $itemData) {
                FaqItem::create([
                    ...$itemData,
                    'faq_category_id' => $category->id,
                    'sort_order' => $index,
                    'status' => FaqItem::STATUS_PUBLISHED,
                ]);
            }
        }
    }
}
