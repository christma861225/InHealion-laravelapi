<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserdetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userdetail', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('details');
            $table->string('dob');
            $table->string('email');
            $table->string('history');
            $table->string('login');
            $table->string('password');
            $table->string('phone_number');
            $table->string('postcode');
            $table->string('surname');
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
        Schema::dropIfExists('userdetail');
    }
}
