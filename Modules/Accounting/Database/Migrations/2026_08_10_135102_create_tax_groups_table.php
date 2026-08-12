<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * ایجاد جدول tax_groups برای گروه‌بندی مالیات‌ها
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_groups', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');

            $table->string('code', 50)->comment('کد شناسایی گروه مالیاتی (منحصر‌به‌فرد برای هر شرکت)');
            
            $table->string('name', 100)->comment('نام گروه مالیاتی (مثلاً مالیات‌های استاندارد، مالیات‌های ویژه)');
            
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'code']);

            $table->index(['company_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     * حذف جدول tax_groups در صورت نیاز
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_groups');
    }
};