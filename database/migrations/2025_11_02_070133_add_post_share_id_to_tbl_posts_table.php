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
        Schema::table('tbl_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('post_share_id')->nullable()->after('post_is_shared');

            // Optional: If you want to reference the shared post
            $table->foreign('post_share_id')
                  ->references('id')
                  ->on('tbl_posts')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_posts', function (Blueprint $table) {
            // Drop the foreign key first before dropping the column
            $table->dropForeign(['post_share_id']);
            $table->dropColumn('post_share_id');
        });
    }
};
