<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Zoom\Entities\ZoomSetting;

class CreateZoomsettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_setting', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('api_key', 50)->nullable();
            $table->string('secret_key', 50)->nullable();
            $table->timestamps();
        });

        $companies =  \App\Company::withoutGlobalScope('active')->get();
        foreach ($companies as $company) {
            $setting = new ZoomSetting();
            $setting->company_id = $company->id;
            $setting->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zoom_setting');
    }
}
