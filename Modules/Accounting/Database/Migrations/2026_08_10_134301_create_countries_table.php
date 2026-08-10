<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول countries با فیلدهای مورد نیاز
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // کد دو حرفی کشور (مثلاً IR, US)
            $table->string('iso2', 2)->unique()->index();
            
            // کد سه حرفی کشور (مثلاً IRN, USA)
            $table->string('iso3', 3)->unique()->index();
            
            // نام کامل کشور (مثلاً Iran, United States)
            $table->string('name', 100);
            
            // کد عددی کشور (مثلاً 364 برای ایران)
            $table->string('numeric_code', 10)->nullable();
            
            // کد تلفن کشور (مثلاً 98+)
            $table->string('phone_code', 10)->nullable();
            
            // نام پایتخت
            $table->string('capital', 100)->nullable();
            
            // وضعیت فعال/غیرفعال (پیش‌فرض فعال)
            $table->boolean('is_active')->default(true);

            // زمان‌های ایجاد و بروزرسانی (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول countries در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
};