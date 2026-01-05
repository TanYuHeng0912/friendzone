<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvancedFiltersToUserSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->string('search_country')->nullable()->after('search_tag3');
            $table->string('search_relationship')->nullable()->after('search_country');
            $table->string('search_has_photos')->nullable()->default('1')->after('search_relationship');
            $table->string('search_online_now')->nullable()->default('0')->after('search_has_photos');
        });
    }

    public function down()
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn(['search_country', 'search_relationship', 'search_has_photos', 'search_online_now']);
        });
    }
}
