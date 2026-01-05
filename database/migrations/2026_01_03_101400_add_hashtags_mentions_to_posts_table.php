<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHashtagsMentionsToPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('hashtags')->nullable()->after('metadata'); // Extracted hashtags
            $table->json('mentions')->nullable()->after('hashtags'); // Extracted user mentions
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['hashtags', 'mentions']);
        });
    }
}

