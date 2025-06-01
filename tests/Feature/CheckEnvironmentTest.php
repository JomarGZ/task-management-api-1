<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckEnvironmentTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $environment = app()->environment();
        $database = config('database.connections.mysql_testing.database');
        
        \Log::info("Current environment: $environment");
        \Log::info("Current database: $database");
        
        $this->assertEquals('testing', $environment, 'Not using the testing environment!');
        $this->assertEquals('task_management_api_1_test', $database, 'Not using the testing database!');
    }
}
