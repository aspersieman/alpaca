<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Group;

class MenuControllerTest extends TestCase
{
  /**
   * List menus super user
   * @test
   * @return void
   */
  public function list_all_menus_super_user()
  {
    $user = factory(User::class)->create();
    $group = Group::where(['name' => 'Super User'])->first();
    $user->groups()->attach($group->id);
    $response = $this
      ->actingAs($user, 'api')
      ->get(route('menu.index'));
    $response->assertOk();
    $this->assertEquals($response['message'], 'List resource ok.');
    $this->assertCount(11, $response['data']);
  }

  /**
   * List menus normal user
   * @test
   * @return void
   */
  public function list_all_menus_normal_user()
  {
    $user = factory(User::class)->create();
    $response = $this
      ->actingAs($user, 'api')
      ->get(route('menu.index'));
    $response->assertOk();
    $this->assertEquals($response['message'], 'List resource ok.');
    $this->assertCount(5, $response['data']);
  }

  /**
   * List menus guest user
   * @test
   * @return void
   */
  public function list_all_menus_guest_user()
  {
    $response = $this->json('GET', route('menu.index'), [], ['Accept' => 'application/json']);
    $response->assertUnauthorized();
    $this->assertEquals($response['message'], 'Unauthenticated.');
  }
}
