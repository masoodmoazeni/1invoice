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
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با شرکت (سیستم چندشرکتی)
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // ارتباط با دفتر روزنامه (در صورت وجود)
            $table->foreignId('journal_id')
                  ->nullable()
                  ->constrained('journals')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->index();

            // ارتباط با سال مالی
            $table->foreignId('fiscal_year_id')
                  ->constrained('fiscal_years')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->index();

            // شماره سند (شماره منحصر‌به‌فرد در هر سال مالی)
            $table->string('document_no', 50);
            
            // شماره مرجع (شماره سند مرجع یا فاکتور)
            $table->string('reference_no', 50)->nullable();
            
            // تاریخ سند
            $table->date('document_date');
            
            // تاریخ ثبت در سیستم
            $table->date('posting_date')->nullable();
            
            // وضعیت سند
            $table->enum('status', [
                'draft',       // پیش‌نویس
                'pending',     // در انتظار تایید
                'approved',    // تایید شده
                'posted',      // ثبت نهایی
                'rejected',    // رد شده
                'voided'       // باطل شده
            ])->default('draft')->index();

            // ارز سند
            $table->foreignId('currency_id')
                  ->constrained('currencies')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->index();

            // نرخ ارز (در صورت استفاده از ارز غیر از ارز پایه)
            $table->decimal('exchange_rate', 10, 4)->default(1);

            // شرح سند
            $table->text('description')->nullable();

            // مجموع بدهکار و بستانکار (برای اعتبارسنجی)
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);

            // ایجاد کننده سند
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // تایید کننده سند
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // تاریخ تایید
            $table->timestamp('approved_at')->nullable();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();
            
            // حذف نرم (soft delete)
            $table->softDeletes();

            // ایندکس ترکیبی برای جلوگیری از تکرار شماره سند در هر سال مالی
            $table->unique(['company_id', 'fiscal_year_id', 'document_no']);

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
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