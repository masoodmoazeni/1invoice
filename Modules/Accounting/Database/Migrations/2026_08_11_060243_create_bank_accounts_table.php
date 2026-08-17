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
            $table->id();

            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('partner_id');

            $table->string('bank_name', 100)->comment('نام بانک');
            
            $table->string('branch_name', 100)->comment('نام شعبه بانک')->nullable();
            
            $table->string('account_number', 50)->comment('شماره حساب بانکی');
            
            $table->string('iban', 34)->comment('شماره شبا (IBAN - International Bank Account Number)')->nullable()->unique();
            
            $table->string('swift', 11)->comment('کد سوئیفت (Swift/BIC Code)')->nullable();
            
            $table->unsignedBigInteger('currency_id')->comment('ارز حساب بانکی');

            $table->boolean('is_default')->comment('آیا این حساب بانکی به‌عنوان پیش‌فرض استفاده شود؟')->default(false)->index();
            
            $table->boolean('is_active')->comment('وضعیت فعال/غیرفعال')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'account_number']);

            
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