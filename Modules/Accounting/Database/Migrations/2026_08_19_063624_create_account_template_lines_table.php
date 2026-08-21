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
            $table->id();

            $table->unsignedBigInteger('template_id')->comment('ارتباط با قالب - در صورت حذف قالب، تمام ردیف‌های مربوطه نیز حذف می‌شوند');

            $table->string('account_code', 50)->comment('کد حساب (ساختار سلسله‌مراتبی مانند: 1, 1.1, 1.1.1)');
            
            $table->string('account_name', 100)->comment('نام حساب');
            
            $table->string('parent_code', 50)->comment('کد حساب والد (برای ساختار سلسله‌مراتبی)')->nullable();
            
            $table->enum('category', [
                'asset',      // دارایی
                'liability',  // بدهی
                'equity',     // سرمایه
                'revenue',    // درآمد
                'expense'     // هزینه
            ])->comment('دسته‌بندی حساب (دارایی، بدهی، سرمایه، درآمد، هزینه)')->index();
            
            $table->enum('type', ['detail', 'header'])->comment('نوع حساب (جزئی یا کل)')->default('detail');
            
            $table->enum('normal_balance', ['debit', 'credit'])->comment('مانده عادی (بدهکار یا بستانکار)')->default('debit');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['template_id', 'account_code']);

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
