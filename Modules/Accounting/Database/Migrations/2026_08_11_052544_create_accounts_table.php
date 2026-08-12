<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول accounts برای مدیریت حساب‌های مالی (نمودار حساب‌ها)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (برای سیستم‌های چندشرکتی)
            $table->unsignedBigInteger('company_id');

            // کد حساب (ساختار سلسله‌مراتبی مانند: 1, 1.1, 1.1.1)
            $table->string('account_code', 50);
            
            // نام حساب
            $table->string('account_name', 100);
            
            // حساب والد (برای ساختار سلسله‌مراتبی)
            $table->unsignedBigInteger('parent_id');

            // دسته‌بندی حساب (دارایی، بدهی، سرمایه، درآمد، هزینه)
            $table->enum('account_category', [
                'asset',      // دارایی
                'liability',  // بدهی
                'equity',     // سرمایه
                'revenue',    // درآمد
                'expense'     // هزینه
            ])->index();

            // نوع حساب (جزئی یا کل)
            $table->enum('account_type', ['detail', 'header'])->default('detail');

            // مانده عادی (بدهکار یا بستانکار)
            $table->enum('normal_balance', ['debit', 'credit'])->default('debit');

            // ارز حساب (در صورت نیاز به ارز خاص)
            $table->unsignedBigInteger('currency_id');

            // آیا اجازه ثبت سند در این حساب وجود دارد؟
            $table->boolean('allow_posting')->default(true);

            // آیا این حساب سیستمی است (غیرقابل حذف یا تغییر توسط کاربر)
            $table->boolean('is_system')->default(false);

            // وضعیت فعال/غیرفعال
            $table->boolean('is_active')->default(true)->index();

            // سطح حساب در ساختار سلسله‌مراتبی (به‌صورت خودکار محاسبه می‌شود)
            $table->unsignedInteger('level')->default(0);

            // ترتیب نمایش
            $table->unsignedInteger('sort_order')->default(0);

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();
            
            // حذف نرم (soft delete)
            $table->softDeletes();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد حساب در هر شرکت
            $table->unique(['company_id', 'account_code']);

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['company_id', 'account_category']);
            $table->index(['company_id', 'is_active']);
            $table->index(['company_id', 'account_type']);
            $table->index(['parent_id', 'level']);
            $table->index(['account_category', 'normal_balance']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول accounts در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};