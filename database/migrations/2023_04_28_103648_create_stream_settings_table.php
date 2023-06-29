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
        Schema::create('stream_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('stream_id')->nullable();
            $table->integer('type')->nullable();
            $table->longText('interval')->nullable();
            $table->longText('date')->nullable();
            $table->longText('daily')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stream_settings');
    }
};
