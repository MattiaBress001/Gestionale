<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('has_esn_card', 12)->change();
        });
    }

    public function down()
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->integer('has_esn_card')->change();
        });
    }
};
