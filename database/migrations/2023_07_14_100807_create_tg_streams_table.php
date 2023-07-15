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
        Schema::create('tg_streams', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->nullable();
            $table->integer('channel_id')->default(0);
            $table->longText('title')->nullable();
            $table->string('link')->nullable();
            $table->integer('active')->default(0);
            $table->integer('live')->default(0);
            $table->integer('start_count')->default(0);
            $table->longText('error_fix')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tg_streams');
    }
};
