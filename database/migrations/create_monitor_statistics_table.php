<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateMonitorStatisticsTable
 */
class CreateMonitorStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitor_statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('monitor_id');
            $table->float('total_time', 8, 6);
            $table->float('namelookup_time', 8, 6);
            $table->float('connect_time', 8, 6);
            $table->float('pretransfer_time', 8, 6);
            $table->float('starttransfer_time', 8, 6);
            $table->float('redirect_time', 8, 6);
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
        Schema::dropIfExists('monitor_statistics');
    }
}
