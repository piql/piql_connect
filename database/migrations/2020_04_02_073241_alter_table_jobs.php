<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Job;

class AlterTableJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->bigInteger('size')->default(0);
        });

        App\Job::all()->map(function($job) {
           $job->size = $job->aips->map(function ($aip) {
                return $aip->size;
            })->sum();
           $job->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->removeColumn('size');
        });
    }
}
