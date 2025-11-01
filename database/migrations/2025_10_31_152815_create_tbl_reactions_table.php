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
        Schema::create('tbl_reactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('reaction_post_id');
            $table->foreign('reaction_post_id')
                  ->references('id')
                  ->on('tbl_posts')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('reaction_user_id');
            $table->foreign('reaction_user_id')
                  ->references('id')
                  ->on('tbl_users')
                  ->onDelete('cascade');

            $table->string('reaction_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_reactions');
    }
};
