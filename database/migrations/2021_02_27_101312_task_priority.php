<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TaskPriority extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('tasks', function (Blueprint $table) {
        if (DB::connection() instanceof Illuminate\Database\SQLiteConnection) {
          $table->string('priority')->default(0);
        } else {
          $table->integer('priority')->unsigned()->default(0)->after('due_time');
          $table->dropColumn(['order']);
        }
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('tasks', function (Blueprint $table) {
        $table->integer('order')->unsigned()->default(0)->after('due_time');
        $table->dropColumn(['priority']);
      });
    }
}
