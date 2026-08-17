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
            $table->id();

            $table->unsignedBigInteger('from_currency_id')->comment('ارز مبدا');

            $table->unsignedBigInteger('to_currency_id')->comment('ارز مقصد');

            $table->decimal('rate', 15, 6)->comment('نرخ ارز (مقدار to_currency به ازای 1 واحد from_currency)')->default(1);

            $table->date('effective_date')->comment('تاریخ اعتبار نرخ ارز')->index();

            $table->string('source', 100)->comment('منبع دریافت نرخ ارز (API، دستی، بانک مرکزی و ...)')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['from_currency_id', 'to_currency_id', 'effective_date'], 'unique_exchange_rate');

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