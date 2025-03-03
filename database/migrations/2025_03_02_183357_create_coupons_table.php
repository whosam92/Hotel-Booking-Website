<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('coupons', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->decimal('discount_amount', 10, 2);
        $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed'); // Fixed or percentage discount
        $table->unsignedBigInteger('hotel_id')->nullable(); // Null means global coupon
        $table->boolean('status')->default(1); // 1 = Active, 0 = Inactive
        $table->timestamps();

        $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
};
