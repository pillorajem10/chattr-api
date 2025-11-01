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
        Schema::create('tbl_posts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('post_user_id');
            $table->foreign('post_user_id')
                  ->references('id')
                  ->on('tbl_users')
                  ->onDelete('cascade');

            $table->text('post_content');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_posts');
    }
};
