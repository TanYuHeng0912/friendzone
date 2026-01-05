<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderToPicturesTable extends Migration
{
    public function up()
    {
        Schema::table('pictures', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('path');
        });
    }

    public function down()
    {
        Schema::table('pictures', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
