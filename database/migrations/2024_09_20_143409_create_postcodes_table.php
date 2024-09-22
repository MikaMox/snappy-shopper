<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('postcodes', function (Blueprint $table) {
            $table->id();
            $table->string('postcode');
            $table->float('latitude');
            $table->float('longitude');
            $table->timestamps();
            $table->index(['postcode, latitude', 'longitude']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('postcodes');
    }
};
