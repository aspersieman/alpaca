<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
  use CreatesApplication, RefreshDatabase;

  public $apiBaseUrl = '/api/v1/';

  protected function setUp(): void
  {
    parent::setUp();

    $this->artisan('migrate');
    $this->artisan('passport:install');
    $this->artisan('db:seed');
  }
}
