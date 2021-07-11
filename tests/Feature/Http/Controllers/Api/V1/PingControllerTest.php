<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PingControllerTest extends TestCase
{
  /**
   * Test ping
   * @test
   * @return void
   */
  public function ping()
  {
    $response = $this
      ->get(route('ping.index'));

    $response->assertStatus(200);

    $response->assertSimilarJson([
      'message' => 'pong'
    ]);
  }
}
