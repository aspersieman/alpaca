<?php
namespace Database\Seeders;

use App\Models\Group;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    // create normal user
    $groupDefaultUser = Group::create([
      'name' => Str::random(10),
      'permissions' => []
    ]);
    // generate random users
    $users = factory(User::class,30)->create();
    $users->each(function($u) use ($groupDefaultUser)
    {
      $u->groups()->attach($groupDefaultUser);
    });
  }
}
