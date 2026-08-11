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
            $table->foreignId('journal_entry_id')
                  ->constrained('journal_entries')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // شماره ردیف (برای ترتیب نمایش)
            $table->unsignedInteger('line_no')->default(0);

            // ارتباط با حساب مالی
            $table->foreignId('account_id')
                  ->constrained('accounts')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->index();

            // ارتباط با طرف حساب (مشتری/تامین‌کننده/کارمند و ...)
            $table->foreignId('partner_id')
                  ->nullable()
                  ->constrained('partners')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // ارتباط با پروژه
            $table->foreignId('project_id')
                  ->nullable()
                  ->constrained('projects')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // ارتباط با دپارتمان
            $table->foreignId('department_id')
                  ->nullable()
                  ->constrained('departments')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // ارتباط با مرکز هزینه
            $table->foreignId('cost_center_id')
                  ->nullable()
                  ->constrained('cost_centers')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // شرح ردیف
            $table->text('description')->nullable();

            // مبلغ بدهکار
            $table->decimal('debit', 15, 2)->default(0);
            
            // مبلغ بستانکار
            $table->decimal('credit', 15, 2)->default(0);

            // ارز ردیف (در صورت متفاوت بودن با ارز سند)
            $table->foreignId('currency_id')
                  ->nullable()
                  ->constrained('currencies')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

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