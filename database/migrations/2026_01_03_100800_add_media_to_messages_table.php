<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMediaToMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('media_type')->nullable()->after('message'); // 'image', 'video', 'gif'
            $table->string('media_path')->nullable()->after('media_type');
            $table->string('media_thumbnail')->nullable()->after('media_path');
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'media_path', 'media_thumbnail']);
        });
    }
}

