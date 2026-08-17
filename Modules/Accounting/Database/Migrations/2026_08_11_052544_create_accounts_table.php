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
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->string('account_code', 50)->nullable();
            
            $table->string('account_name', 100)->comment('نام حساب')->nullable();
            
            $table->unsignedBigInteger('parent_id')->comment('حساب والد (برای ساختار سلسله‌مراتبی)')->nullable();

            
            $table->enum('account_category', [
                'asset',      // دارایی
                'liability',  // بدهی
                'equity',     // سرمایه
                'revenue',    // درآمد
                'expense'     // هزینه
            ])->comment('// دسته‌بندی حساب (دارایی، بدهی، سرمایه، درآمد، هزینه)')->nullable()->index();

            $table->enum('account_type', ['detail', 'header'])->nullable()->comment('نوع حساب (جزئی یا کل)')->default('detail');

            $table->enum('normal_balance', ['debit', 'credit'])->nullable()->comment('مانده عادی (بدهکار یا بستانکار)')->default('debit');

            $table->unsignedBigInteger('currency_id')->comment('ارز حساب (در صورت نیاز به ارز خاص)');

            $table->boolean('allow_posting')->nullable()->comment('آیا اجازه ثبت سند در این حساب وجود دارد؟')->default(true);

            $table->boolean('is_system')->nullable()->comment('آیا این حساب سیستمی است (غیرقابل حذف یا تغییر توسط کاربر)')->default(false);

            $table->boolean('is_active')->nullable()->comment('وضعیت فعال/غیرفعال')->default(true)->index();

            $table->unsignedInteger('level')->comment('سطح حساب در ساختار سلسله‌مراتبی (به‌صورت خودکار محاسبه می‌شود)')->default(0);

            $table->unsignedInteger('sort_order')->comment('ترتیب نمایش')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'account_code']);

            $table->index(['company_id', 'account_category']);
            $table->index(['company_id', 'is_active']);
            $table->index(['company_id', 'account_type']);
            $table->index(['parent_id', 'level']);
            $table->index(['account_category', 'normal_balance']);

            $table->comment('جدول اطلاعات حساب‌های مالی قسمت حساب‌ها');
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