<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id'); // Customer instead of User
            $table->unsignedBigInteger('room_id'); // Room being reviewed
            $table->tinyInteger('rating')->unsigned(); // Rating (1-5)
            $table->text('review'); // Review text
            $table->timestamps();

            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('reviews');
    }
};
