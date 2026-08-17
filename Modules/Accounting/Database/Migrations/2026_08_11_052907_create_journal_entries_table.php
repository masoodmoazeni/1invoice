<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول journal_entries برای مدیریت سندهای حسابداری
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id')->comment('ارتباط با شرکت (سیستم چندشرکتی)');

            $table->unsignedBigInteger('journal_id')->comment('ارتباط با دفتر روزنامه (در صورت وجود)');

            $table->unsignedBigInteger('fiscal_year_id')->comment('ارتباط با سال مالی');

            $table->string('document_no', 50)->comment('شماره سند (شماره منحصر‌به‌فرد در هر سال مالی)');
            
            $table->string('reference_no', 50)->comment('شماره مرجع (شماره سند مرجع یا فاکتور)')->nullable();
            
            $table->date('document_date')->comment('تاریخ سند');
            
            $table->date('posting_date')->comment('تاریخ ثبت در سیستم')->nullable();
            
            $table->enum('status', [
                'draft',       // پیش‌نویس
                'pending',     // در انتظار تایید
                'approved',    // تایید شده
                'posted',      // ثبت نهایی
                'rejected',    // رد شده
                'voided'       // باطل شده
            ])->default('draft')->comment('وضعیت سند')->index();

            $table->unsignedBigInteger('currency_id')->comment('ارز سند');

            $table->decimal('exchange_rate', 10, 4)->comment('نرخ ارز (در صورت استفاده از ارز غیر از ارز پایه)')->default(1);

            $table->text('description')->comment('شرح سند')->nullable();

            $table->decimal('total_debit', 15, 2)->comment('مجموع بدهکار و بستانکار (برای اعتبارسنجی)')->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);

            $table->unsignedBigInteger('created_by')->comment('ایجاد کننده سند');

            $table->unsignedBigInteger('approved_by')->comment('تایید کننده سند');

            $table->timestamp('approved_at')->comment('تاریخ تایید')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'fiscal_year_id', 'document_no']);

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'document_date']);
            $table->index(['company_id', 'posting_date']);
            $table->index(['company_id', 'fiscal_year_id', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['document_date', 'posting_date']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول journal_entries در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_entries');
    }
};