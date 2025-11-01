<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_shares', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('share_post_id');
            $table->foreign('share_post_id')
                  ->references('id')
                  ->on('tbl_posts')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('share_user_id');
            $table->foreign('share_user_id')
                  ->references('id')
                  ->on('tbl_users')
                  ->onDelete('cascade');

            $table->text('share_caption')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_shares');
    }
};
