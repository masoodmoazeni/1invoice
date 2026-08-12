<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول exchange_rates برای مدیریت نرخ‌های ارز
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارز مبدا
            $table->unsignedBigInteger('from_currency_id');

            // ارز مقصد
            $table->unsignedBigInteger('to_currency_id');

            // نرخ ارز (مقدار to_currency به ازای 1 واحد from_currency)
            $table->decimal('rate', 15, 6)->default(1);

            // تاریخ اعتبار نرخ ارز
            $table->date('effective_date')->index();

            // منبع دریافت نرخ ارز (API، دستی، بانک مرکزی و ...)
            $table->string('source', 100)->nullable();

            // زمان‌های ایجاد
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار نرخ ارز در یک تاریخ
            $table->unique(['from_currency_id', 'to_currency_id', 'effective_date'], 'unique_exchange_rate');

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['from_currency_id', 'to_currency_id']);
            $table->index(['effective_date', 'from_currency_id']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول exchange_rates در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchange_rates');
    }
};