<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->string('email')->unique();
            $table->integer('balance')->default(0);
            $table->string('partner')->nullable();
            $table->integer('newsletter')->default(1);
            $table->integer('partners_clicks')->default(0);
            $table->integer('vk_account_places')->default(1);
            $table->integer('vk_group_places')->default(1);
            $table->integer('tg_channel_places')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_infos');
    }
};
