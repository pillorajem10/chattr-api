<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_chatrooms', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cr_user_one_id');
            $table->foreign('cr_user_one_id')
                  ->references('id')
                  ->on('tbl_users')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('cr_user_two_id');
            $table->foreign('cr_user_two_id')
                  ->references('id')
                  ->on('tbl_users')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_chatrooms');
    }
};
