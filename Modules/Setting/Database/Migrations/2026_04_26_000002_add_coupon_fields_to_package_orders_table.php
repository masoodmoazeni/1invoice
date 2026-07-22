<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('package_orders', 'coupon_id')) {
                $table->foreignId('coupon_id')
                    ->nullable()
                    ->after('package_type')
                    ->constrained('coupons')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('package_orders', 'coupon_code')) {
                $table->string('coupon_code', 100)->nullable()->after('coupon_id');
            }

            if (!Schema::hasColumn('package_orders', 'original_amount')) {
                $table->decimal('original_amount', 10, 2)->nullable()->after('coupon_code');
            }

            if (!Schema::hasColumn('package_orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('original_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('package_orders', function (Blueprint $table) {
            if (Schema::hasColumn('package_orders', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }

            if (Schema::hasColumn('package_orders', 'original_amount')) {
                $table->dropColumn('original_amount');
            }

            if (Schema::hasColumn('package_orders', 'coupon_code')) {
                $table->dropColumn('coupon_code');
            }

            if (Schema::hasColumn('package_orders', 'coupon_id')) {
                $table->dropConstrainedForeignId('coupon_id');
            }
        });
    }
};
