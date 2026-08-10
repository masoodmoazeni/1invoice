<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول time_zones با فیلدهای مورد نیاز و ارتباط با جدول countries
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_zones', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // کلید خارجی برای ارتباط با جدول countries
            // در صورت حذف کشور، تمام منطقه‌های زمانی مربوطه نیز حذف می‌شوند (CASCADE)
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // نام منطقه زمانی (مثلاً Asia/Tehran, America/New_York)
            $table->string('name', 100)->unique();
            
            // مقدار UTC Offset (مثلاً +03:30, -05:00)
            $table->string('utc_offset', 10);
            
            // وضعیت پیش‌فرض بودن منطقه زمانی برای کشور مربوطه
            // هر کشور فقط یک منطقه زمانی پیش‌فرض می‌تواند داشته باشد
            $table->boolean('is_default')->default(false);

            // زمان‌های ایجاد و بروزرسانی (created_at, updated_at)
            $table->timestamps();

            // ایندکس ترکیبی برای جستجوی سریع‌تر بر اساس کشور و وضعیت پیش‌فرض
            $table->index(['country_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول time_zones در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_zones');
    }
};