<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Task;

class AddComplatedAtColumnToTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
          $table->timestampTz('completed_at')->nullable()->after('status');
        });
        Task::where('status', 'complete')
          ->update(['completed_at' => DB::raw('`created_at`')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
          $table->dropColumn('completed_at');
        });
    }
}
