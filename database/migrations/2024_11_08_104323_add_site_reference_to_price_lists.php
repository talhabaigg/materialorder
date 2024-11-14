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
        Schema::table('price_lists', function (Blueprint $table) {
            $table->string('site_reference')->nullable();
        });
    }

    public function down()
    {
        Schema::table('price_lists', function (Blueprint $table) {
            $table->dropColumn('site_reference');
        });
    }
};
