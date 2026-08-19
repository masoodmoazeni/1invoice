<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول dimension_values برای مدیریت مقادیر ابعاد مالی
     * هر بعد می‌تواند دارای چندین مقدار باشد (مانند مراکز هزینه مختلف)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dimension_values', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('dimension_id')->comment('ارتباط با بعد (هر مقدار متعلق به یک بعد است) - در صورت حذف بعد، تمام مقادیر مربوطه نیز حذف می‌شوند');

            $table->string('code', 50);
            
            $table->string('name', 100);
            
            $table->unsignedBigInteger('parent_id');

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['dimension_id', 'code']);

            $table->index(['dimension_id', 'parent_id']);
            $table->index(['parent_id']);

            $this->comment('ایجاد جدول dimension_values برای مدیریت مقادیر ابعاد مالی');
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول dimension_values در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dimension_values');
    }
};