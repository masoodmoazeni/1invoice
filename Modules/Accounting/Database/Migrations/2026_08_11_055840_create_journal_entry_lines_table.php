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
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با سند حسابداری
            // در صورت حذف سند، تمام ردیف‌های مربوطه نیز حذف می‌شوند
            $table->unsignedBigInteger('journal_entry_id');

            // شماره ردیف (برای ترتیب نمایش)
            $table->unsignedInteger('line_no')->default(0);

            // ارتباط با حساب مالی
            $table->unsignedBigInteger('account_id');

            // ارتباط با طرف حساب (مشتری/تامین‌کننده/کارمند و ...)
            $table->unsignedBigInteger('partner_id');

            // ارتباط با پروژه
            $table->unsignedBigInteger('project_id');

            // ارتباط با دپارتمان
            $table->unsignedBigInteger('department_id');

            // ارتباط با مرکز هزینه
            $table->unsignedBigInteger('cost_center_id');

            // شرح ردیف
            $table->text('description')->nullable();

            // مبلغ بدهکار
            $table->decimal('debit', 15, 2)->default(0);
            
            // مبلغ بستانکار
            $table->decimal('credit', 15, 2)->default(0);

            // ارز ردیف (در صورت متفاوت بودن با ارز سند)
            $table->unsignedBigInteger('currency_id');

            // نرخ ارز (در صورت متفاوت بودن با ارز سند)
            $table->decimal('exchange_rate', 10, 4)->nullable();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();
            
            // حذف نرم (soft delete)
            $table->softDeletes();

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
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