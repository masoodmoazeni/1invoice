<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول account_templates برای مدیریت قالب‌های نمودار حساب‌ها
     * هر قالب شامل مجموعه‌ای از حساب‌های از پیش تعریف شده است
     * که می‌تواند بر اساس کشور یا استانداردهای مختلف باشد
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_templates', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با کشور
            // هر قالب می‌تواند مختص یک کشور باشد
            // در صورت حذف کشور، قالب‌های مربوطه نیز حذف می‌شوند
            $table->unsignedBigInteger('country_id');

            // نام قالب (مثلاً "نمودار حساب‌های استاندارد ایران")
            $table->string('name', 100);
            
            // نسخه قالب (برای مدیریت تغییرات و به‌روزرسانی‌ها)
            $table->string('version', 20)->default('1.0.0');

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار نام قالب در هر کشور
            $table->unique(['country_id', 'name']);

            // ایندکس ترکیبی برای جستجوی سریع‌تر
            $table->index(['country_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول account_templates در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_templates');
    }
};