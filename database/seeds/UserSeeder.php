<?php

use App\Components\User\Models\Group;
use App\Components\User\Models\Permission;
use App\Components\User\Models\User;
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
    // create the super user permission
    $permissionSuperUser = Permission::create([
      'title' => 'Super User',
      'description' => 'Superuser permission',
      'key' => Permission::SUPER_USER_PERMISSION_KEY,
    ]);

    // create all other permissions
    $permissionSample1 = Permission::create([
      'title' => 'User Create',
      'description' => 'Permission to create user.',
      'key' => 'user.create',
    ]);
    $permissionSample2 = Permission::create([
      'title' => 'User Edit',
      'description' => 'Permission to edit user.',
      'key' => 'user.edit',
    ]);
    $permissionSample3 = Permission::create([
      'title' => 'User Delete',
      'description' => 'Permission to delete user.',
      'key' => 'user.delete',
    ]);

    // create super user group
    $groupSuperUser = Group::create([
      'name' => Group::SUPER_USER_GROUP_NAME,
      'permissions' => [
        [
          'title' => 'Super User',
          'description' => 'Superuser permission',
          'key' => Permission::SUPER_USER_PERMISSION_KEY,
          'value' => 1
        ]
      ]
    ]);
    $groupSuperUser->addPermission($permissionSuperUser,Permission::PERMISSION_ALLOW);

    // create normal user
    $groupDefaultUser = Group::create([
      'name' => Group::DEFAULT_USER_GROUP_NAME,
      'permissions' => []
    ]);

    // create admin account
    $AdminUser = User::create([
      'name' => 'Nicol',
      'email' => 'aspersieman@gmail.com',
      'password' => '326EyWQj&EqV@YX2',
      'remember_token' => Str::random(10),
      'permissions' => [],
      'last_login' => \Carbon\Carbon::now(),
      'active' => \Carbon\Carbon::now(),
      'activation_key' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
    ]);

    // make super user
    $AdminUser->groups()->attach($groupSuperUser);

    // generate random users
    /*
    $users = factory(User::class,30)->create();
    $users->each(function($u) use ($groupDefaultUser)
    {
      $u->groups()->attach($groupDefaultUser);
    });
     */
  }
}
