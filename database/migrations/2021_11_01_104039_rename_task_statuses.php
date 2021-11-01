<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Task;

class RenameTaskStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Task::where('status', 'ongoing')
          ->update(['status' => 'inprogress']);
        Task::where('status', 'overdue')
          ->update(['status' => 'todo']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Task::where('status', 'inprogress')
          ->update(['status' => 'ongoing']);
        Task::where('status', 'todo')
          ->update(['status' => 'overdue']);
    }
}
