<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول journal_entry_details برای جزئیات سند حسابداری
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_entry_details', function (Blueprint $table) {
            $table->id();

            // ارتباط با سند اصلی
            $table->foreignId('journal_entry_id')
                  ->constrained('journal_entries')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // ارتباط با حساب
            $table->foreignId('account_id')
                  ->constrained('accounts')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->index();

            // مبلغ بدهکار
            $table->decimal('debit', 15, 2)->default(0);
            
            // مبلغ بستانکار
            $table->decimal('credit', 15, 2)->default(0);

            // شرح ردیف
            $table->text('description')->nullable();

            // اطلاعات تکمیلی
            $table->string('reference_no', 50)->nullable();
            $table->date('reference_date')->nullable();

            // مرکز هزینه (در صورت وجود)
            $table->foreignId('cost_center_id')
                  ->nullable()
                  ->constrained('cost_centers')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // پروژه (در صورت وجود)
            $table->foreignId('project_id')
                  ->nullable()
                  ->constrained('projects')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();
            
            // حذف نرم
            $table->softDeletes();

            // ایندکس‌ها
            $table->index(['account_id', 'debit', 'credit']);
            $table->index(['cost_center_id']);
            $table->index(['project_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_entry_details');
    }
};