<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول journal_entry_lines برای جزئیات ردیف‌های سند حسابداری
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('journal_entry_id')->comment('ارتباط با سند حسابداری - در صورت حذف سند، تمام ردیف‌های مربوطه نیز حذف می‌شوند');

            $table->unsignedInteger('line_no')->comment('شماره ردیف (برای ترتیب نمایش)')->default(0);

            $table->unsignedBigInteger('account_id')->comment('ارتباط با حساب مالی');

            $table->unsignedBigInteger('partner_id')->comment('ارتباط با طرف حساب (مشتری/تامین‌کننده/کارمند و ...)');

            $table->unsignedBigInteger('project_id')->comment('ارتباط با پروژه');

            $table->unsignedBigInteger('department_id')->comment('ارتباط با دپارتمان');

            $table->unsignedBigInteger('cost_center_id')->comment('ارتباط با مرکز هزینه');

            $table->text('description')->comment('شرح ردیف')->nullable();

            $table->decimal('debit', 15, 2)->comment('مبلغ بدهکار')->default(0);
            
            $table->decimal('credit', 15, 2)->comment('مبلغ بستانکار')->default(0);

            $table->unsignedBigInteger('currency_id')->comment('ارز ردیف (در صورت متفاوت بودن با ارز سند)');

            $table->decimal('exchange_rate', 10, 4)->comment('نرخ ارز (در صورت متفاوت بودن با ارز سند)')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['journal_entry_id', 'line_no']);
            $table->index(['account_id', 'debit', 'credit']);
            $table->index(['partner_id']);
            $table->index(['project_id']);
            $table->index(['department_id']);
            $table->index(['cost_center_id']);
            $table->index(['currency_id']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول journal_entry_lines در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_entry_lines');
    }
};