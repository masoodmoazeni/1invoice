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
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (سیستم چندشرکتی)
            // در صورت حذف شرکت، تمام انواع اسناد مربوطه نیز حذف می‌شوند
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // کد شناسایی نوع سند (منحصر‌به‌فرد برای هر شرکت)
            $table->string('code', 50);
            
            // نام نوع سند
            $table->string('name', 100);
            
            // ارتباط با دفتر روزنامه
            // تعیین می‌کند که اسناد این نوع در کدام دفتر روزنامه ثبت شوند
            $table->foreignId('journal_id')
                  ->nullable()
                  ->constrained('journals')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // ارتباط با شماره‌گذاری
            // تعیین می‌کند که اسناد این نوع از کدام دنباله شماره‌گذاری استفاده کنند
            $table->foreignId('number_sequence_id')
                  ->nullable()
                  ->constrained('number_sequences')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // وضعیت فعال/غیرفعال
            $table->boolean('is_active')->default(true)->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد نوع سند در هر شرکت
            $table->unique(['company_id', 'code']);

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['company_id', 'is_active']);
            $table->index(['company_id', 'journal_id']);
            $table->index(['company_id', 'number_sequence_id']);
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
