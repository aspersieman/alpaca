<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class FixTimers extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    $timers = DB::table('timers')->get(['id', 'started_at', 'stopped_at', 'duration']);
    foreach ($timers as $timer) {
      $tz = config('app.timezone');
      $start = Carbon::createFromFormat('Y-m-d H:i:s', $timer->started_at, $tz);
      $stop = Carbon::createFromFormat('Y-m-d H:i:s', $timer->stopped_at, $tz);
      $duration = $stop->diffInSeconds($start);
      DB::table('timers')
        ->where('id', $timer->id)
        ->update(['duration' => $duration]);
    }
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    //
  }
}
