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
        Schema::create('tg_post_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('post_id')->nullable();
            $table->integer('remove_id')->nullable();
            $table->string('type')->nullable();
            $table->integer('interval')->nullable();
            $table->timestamp('date')->nullable();
            $table->integer('daily')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tg_post_settings');
    }
};
