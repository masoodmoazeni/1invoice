<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول account_template_lines برای جزئیات ردیف‌های حساب در قالب
     * هر ردیف نشان‌دهنده یک حساب در نمودار حساب‌های از پیش تعریف شده است
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_template_lines', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با قالب
            // در صورت حذف قالب، تمام ردیف‌های مربوطه نیز حذف می‌شوند
            $table->foreignId('template_id')
                  ->constrained('account_templates')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // کد حساب (ساختار سلسله‌مراتبی مانند: 1, 1.1, 1.1.1)
            $table->string('account_code', 50);
            
            // نام حساب
            $table->string('account_name', 100);
            
            // کد حساب والد (برای ساختار سلسله‌مراتبی)
            $table->string('parent_code', 50)->nullable();
            
            // دسته‌بندی حساب (دارایی، بدهی، سرمایه، درآمد، هزینه)
            $table->enum('category', [
                'asset',      // دارایی
                'liability',  // بدهی
                'equity',     // سرمایه
                'revenue',    // درآمد
                'expense'     // هزینه
            ])->index();
            
            // نوع حساب (جزئی یا کل)
            $table->enum('type', ['detail', 'header'])->default('detail');
            
            // مانده عادی (بدهکار یا بستانکار)
            $table->enum('normal_balance', ['debit', 'credit'])->default('debit');

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار کد حساب در هر قالب
            $table->unique(['template_id', 'account_code']);

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['template_id', 'category']);
            $table->index(['template_id', 'parent_code']);
            $table->index(['template_id', 'type']);
            $table->index(['parent_code']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول account_template_lines در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_template_lines');
    }
};
