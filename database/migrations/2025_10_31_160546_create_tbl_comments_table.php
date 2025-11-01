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
        Schema::create('tbl_comments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('comment_post_id');
            $table->foreign('comment_post_id')
                  ->references('id')
                  ->on('tbl_posts')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('comment_user_id');
            $table->foreign('comment_user_id')
                  ->references('id')
                  ->on('tbl_users')
                  ->onDelete('cascade');

            $table->text('comment_content');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_comments');
    }
};
