<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول document_types برای مدیریت انواع اسناد در سیستم حسابداری
     * هر نوع سند شامل تنظیمات مربوط به شماره‌گذاری و دفتر روزنامه است
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->string('code', 50)->comment('کد شناسایی نوع سند (منحصر‌به‌فرد برای هر شرکت)');
            
            // نام نوع سند
            $table->string('name', 100)->comment('نام نوع سند');
            
            // ارتباط با دفتر روزنامه
            // تعیین می‌کند که اسناد این نوع در کدام دفتر روزنامه ثبت شوند
            $table->unsignedBigInteger('journal_id');

            // ارتباط با شماره‌گذاری
            // تعیین می‌کند که اسناد این نوع از کدام دنباله شماره‌گذاری استفاده کنند
            $table->unsignedBigInteger('number_sequence_id');

            // وضعیت فعال/غیرفعال
            $table->boolean('is_active')->default(true)->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            $table->unique(['company_id', 'code']);

        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول document_types در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_types');
    }
};
