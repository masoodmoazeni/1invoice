<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول fiscal_years برای مدیریت سال‌های مالی
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fiscal_years', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (برای سیستم‌های چندشرکتی)
            // در صورت حذف شرکت، تمام سال‌های مالی مربوطه نیز حذف می‌شوند
            $table->unsignedBigInteger('company_id');

            // نام سال مالی (مثلاً سال مالی ۱۴۰۳)
            $table->string('name', 100);
            
            // تاریخ شروع سال مالی
            $table->date('start_date');
            
            // تاریخ پایان سال مالی
            $table->date('end_date');
            
            // وضعیت سال مالی: 
            // open = باز و قابل استفاده
            // closed = بسته شده
            // pending = در انتظار
            // locked = قفل شده
            $table->enum('status', ['open', 'closed', 'pending', 'locked'])
                  ->default('pending')
                  ->index();
            
            // آیا این سال مالی به‌عنوان سال مالی پیش‌فرض شرکت است؟
            $table->boolean('is_default')->default(false)->index();
            
            // تاریخ قفل شدن سال مالی (برای جلوگیری از تغییرات پس از این تاریخ)
            $table->timestamp('lock_date')->nullable();
            
            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();
            
            // حذف نرم (soft delete)
            $table->softDeletes();

            // ایندکس ترکیبی برای جلوگیری از تداخل تاریخ‌ها در یک شرکت
            // دو سال مالی نمی‌توانند تاریخ‌های همپوشانی داشته باشند
            $table->unique(['company_id', 'start_date']);
            $table->unique(['company_id', 'end_date']);

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'is_default']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول fiscal_years در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fiscal_years');
    }
};