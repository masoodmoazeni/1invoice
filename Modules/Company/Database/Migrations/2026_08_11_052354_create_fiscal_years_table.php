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
            $table->id();

            $table->unsignedBigInteger('company_id')->comment(' در صورت حذف شرکت، تمام سال‌های مالی مربوطه نیز حذف می‌شوند - ارتباط با شرکت (برای سیستم‌های چندشرکتی)');

            $table->string('name', 100)->comment('نام سال مالی (مثلاً سال مالی ۱۴۰۳)');
            
            $table->date('start_date')->comment('تاریخ شروع سال مالی');
            
            $table->date('end_date')->comment('تاریخ پایان سال مالی');
            
            // وضعیت سال مالی: 
            // open = باز و قابل استفاده
            // closed = بسته شده
            // pending = در انتظار
            // locked = قفل شده
            $table->enum('status', ['open', 'closed', 'pending', 'locked'])
                  ->default('pending')
                  ->index();
            
            $table->boolean('is_default')->comment('آیا این سال مالی به‌عنوان سال مالی پیش‌فرض شرکت است؟')->default(false)->index();
            
            $table->timestamp('lock_date')->comment('تاریخ قفل شدن سال مالی (برای جلوگیری از تغییرات پس از این تاریخ)')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'start_date']);
            $table->unique(['company_id', 'end_date']);

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