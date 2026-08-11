<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول journal_entry_line_dimensions برای ارتباط چندگانه ابعاد با ردیف‌های سند
     * این جدول امکان اتصال هر ردیف سند به چندین بعد مالی را فراهم می‌کند
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_entry_line_dimensions', function (Blueprint $table) {
            // شناسه اصلی (auto-increment)
            $table->id();

            // ارتباط با ردیف سند حسابداری
            // در صورت حذف ردیف سند، تمام ارتباطات ابعاد آن نیز حذف می‌شوند
            $table->foreignId('journal_entry_line_id')
                  ->constrained('journal_entry_lines')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // ارتباط با مقدار بعد
            // در صورت حذف مقدار بعد، ارتباطات آن نیز حذف می‌شوند
            $table->foreignId('dimension_value_id')
                  ->constrained('dimension_values')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->index();

            // زمان‌های ایجاد و بروزرسانی
            $table->timestamps();

            // ایندکس ترکیبی برای جلوگیری از تکرار ارتباط
            // یک ردیف سند نمی‌تواند دو بار به یک مقدار بعد متصل شود
            $table->unique(
                ['journal_entry_line_id', 'dimension_value_id'],
                'unique_line_dimension'
            );

            // ایندکس‌های ترکیبی برای جستجوی سریع‌تر
            $table->index(['dimension_value_id', 'journal_entry_line_id']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول journal_entry_line_dimensions در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_entry_line_dimensions');
    }
};