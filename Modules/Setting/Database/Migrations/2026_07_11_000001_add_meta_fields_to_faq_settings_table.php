<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_META_TITLE =
        'Frequently Asked Questions | Selling a Salon, Spa & Barbershop | SalonSpa Connection';

    private const DEFAULT_META_DESCRIPTION =
        'Answers to common questions about selling a salon, spa, or barbershop — valuations, timelines, commissions, and working with a beauty industry broker.';

    public function up(): void
    {
        Schema::table('faq_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('faq_settings', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('schema');
            }

            if (!Schema::hasColumn('faq_settings', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });

        if (Schema::hasTable('faq_settings')) {
            $now = now();

            if (!DB::table('faq_settings')->exists()) {
                DB::table('faq_settings')->insert([
                    'schema' => null,
                    'meta_title' => self::DEFAULT_META_TITLE,
                    'meta_description' => self::DEFAULT_META_DESCRIPTION,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                foreach (DB::table('faq_settings')->get() as $row) {
                    DB::table('faq_settings')
                        ->where('id', $row->id)
                        ->update([
                            'meta_title' => $row->meta_title ?? self::DEFAULT_META_TITLE,
                            'meta_description' => $row->meta_description ?? self::DEFAULT_META_DESCRIPTION,
                            'updated_at' => $now,
                        ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('faq_settings', function (Blueprint $table) {
            if (Schema::hasColumn('faq_settings', 'meta_description')) {
                $table->dropColumn('meta_description');
            }

            if (Schema::hasColumn('faq_settings', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
        });
    }
};
