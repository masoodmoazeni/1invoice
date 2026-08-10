<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // شناسه شرکت (کد یکتا)
            $table->string('code', 50)->unique()->comment('کد یکتای شرکت');
            
            // اطلاعات اصلی
            $table->string('name', 255)->comment('نام شرکت');
            $table->string('legal_name', 255)->nullable()->comment('نام حقوقی شرکت');
            
            // روابط خارجی
            $table->unsignedBigInteger('country_id')->nullable()->comment('شناسه کشور');
            $table->unsignedBigInteger('base_currency_id')->nullable()->comment('شناسه ارز پایه');
            $table->unsignedBigInteger('language_id')->nullable()->comment('شناسه زبان');
            $table->unsignedBigInteger('timezone_id')->nullable()->comment('شناسه منطقه زمانی');
            
            // اطلاعات مالی و حقوقی
            $table->string('tax_number', 50)->nullable()->comment('شماره مالیاتی');
            $table->string('registration_number', 50)->nullable()->comment('شماره ثبت شرکت');
            
            // اطلاعات تماس
            $table->string('phone', 20)->nullable()->comment('شماره تلفن');
            $table->string('mobile', 20)->nullable()->comment('شماره همراه');
            $table->string('email', 255)->nullable()->comment('آدرس ایمیل');
            $table->string('website', 255)->nullable()->comment('آدرس وبسایت');
            
            // اطلاعات آدرس
            $table->text('address')->nullable()->comment('آدرس کامل');
            $table->string('postal_code', 20)->nullable()->comment('کد پستی');
            $table->string('city', 100)->nullable()->comment('شهر');
            $table->string('state', 100)->nullable()->comment('استان');
            
            // لوگو
            $table->string('logo', 255)->nullable()->comment('مسیر فایل لوگو');
            
            // تنظیمات حسابداری
            $table->integer('fiscal_year_start_month')->default(1)->comment('ماه شروع سال مالی (1-12)');
            
            // وضعیت
            $table->boolean('is_active')->default(true)->comment('وضعیت فعال/غیرفعال');
            
            // زمان‌ها
            $table->timestamps(); // created_at و updated_at
            $table->softDeletes(); // deleted_at
            
            // ایندکس‌ها برای بهبود عملکرد
            $table->index('code');
            $table->index('name');
            $table->index('country_id');
            $table->index('base_currency_id');
            $table->index('is_active');
            $table->index('city');
            $table->index('state');
            
            // توضیحات جدول
            $table->comment('جدول اطلاعات شرکت‌های سیستم حسابداری');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
