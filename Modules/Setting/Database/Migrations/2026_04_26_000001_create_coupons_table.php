<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // if (Schema::hasTable('coupons')) {
        //     return;
        // }

        // Schema::create('coupons', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('code', 100)->unique();
        //     $table->string('type', 20);
        //     $table->decimal('discount_value', 10, 2)->default(0);
        //     $table->unsignedInteger('max_usage')->nullable();
        //     $table->unsignedInteger('used_count')->default(0);
        //     $table->timestamp('expires_at')->nullable();
        //     $table->boolean('is_active')->default(true);
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
