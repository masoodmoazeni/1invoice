<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول bank_accounts برای مدیریت حساب‌های بانکی
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (سیستم چندشرکتی)
            // در صورت حذف شرکت، تمام حساب‌های بانکی مربوطه حذف می‌شوند
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // ارتباط با طرف حساب (در صورت وجود)
            // می‌تواند برای مشتری، تامین‌کننده، کارمند و غیره باشد
            $table->foreignId('partner_id')
                  ->nullable()
                  ->constrained('partners')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // نام بانک
            $table->string('bank_name', 100);
            
            // نام شعبه بانک
            $table->string('branch_name', 100)->nullable();
            
            // شماره حساب بانکی
            $table->string('account_number', 50);
            
            // شماره شبا (IBAN - International Bank Account Number)
            $table->string('iban', 34)->nullable()->unique();
            
            // کد سوئیفت (Swift/BIC Code)
            $table->string('swift', 11)->nullable();
            
            // ارز حساب بانکی
            $table->foreignId('currency_id')
                  ->constrained('currencies')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->index();

            // آیا این حساب بانکی به‌عنوان پیش‌فرض استفاده شود؟
            $table->boolean('is_default')->default(false)->index();
            
            // وضعیت فعال/غیرفعال
            $table->boolean('is_active')->default(true)->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار شماره حساب در هر شرکت
            $table->unique(['company_id', 'account_number']);

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['company_id', 'partner_id']);
            $table->index(['company_id', 'currency_id']);
            $table->index(['company_id', 'is_default', 'is_active']);
            $table->index(['bank_name', 'branch_name']);
            $table->index(['iban']);
            $table->index(['swift']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول bank_accounts در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_accounts');
    }
};