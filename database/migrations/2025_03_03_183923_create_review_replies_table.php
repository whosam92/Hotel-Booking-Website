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
    Schema::create('review_replies', function (Blueprint $table) {
        $table->id();
        $table->foreignId('review_id')->constrained('reviews')->onDelete('cascade');
        $table->foreignId('admin_id')->constrained('users')->onDelete('cascade'); // Assuming admins are stored in the users table
        $table->text('reply');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_replies');
    }
};
