<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirmwareFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firmware_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('firmware_id');
            $table->string('hash')->nullable(false);
            $table->string('file_name');
            $table->timestamps();
            $table->foreign('firmware_id')->references('id')->on('firmware');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('firmware_files');
    }
}
