<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMeetingAppColumnSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zoom_setting', function (Blueprint $table) {
            $table->string('meeting_app')->default('in_app');
        });

        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->string('password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zoom_setting', function (Blueprint $table) {
            $table->dropColumn(['meeting_app']);
        });

        Schema::table('zoom_meetings', function (Blueprint $table) {
            $table->dropColumn(['password']);
        });
    }
}
