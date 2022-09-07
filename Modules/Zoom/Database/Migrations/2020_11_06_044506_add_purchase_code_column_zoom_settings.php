<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Zoom\Entities\ZoomSetting;

class AddPurchaseCodeColumnZoomSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zoom_setting', function (Blueprint $table) {
            $table->string('purchase_code')->nullable();
            $table->timestamp('supported_until')->nullable();
        });

        $settings =  ZoomSetting::orderBy('id', 'desc')->get();
        foreach ($settings as $setting) {
            $setting->id = $setting->id+1;
            $setting->save();
        }

        $setting = new ZoomSetting();
        $setting->id = 1;
        $setting->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zoom_setting', function (Blueprint $table) {
            $table->dropColumn(['purchase_code', 'supported_until']);
        });
    }
}
