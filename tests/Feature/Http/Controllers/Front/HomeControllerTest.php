<?php

namespace Tests\Feature\Http\Controllers\Front;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    /**
     * Can load SPA
     * @test
     * @return void
     */
    public function can_load_spa()
    {
        $response = $this->get(route('home.index', 'all'));

        $response->assertStatus(200);
    }
}
