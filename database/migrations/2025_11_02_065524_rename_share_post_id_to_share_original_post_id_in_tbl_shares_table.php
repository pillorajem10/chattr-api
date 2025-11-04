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
        Schema::table('tbl_shares', function (Blueprint $table) {
            $table->renameColumn('share_post_id', 'share_original_post_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_shares', function (Blueprint $table) {
            $table->renameColumn('share_original_post_id', 'share_post_id');
        });
    }
};
