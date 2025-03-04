<?php

use Tests\TestCase;

class ExampleTest extends TestCase {
    public function testEnvironmentAndDatabase()
    {
        $environment = app()->environment();
        $database = config('database.connections.mysql_testing.database');
        
        \Log::info("Current environment: $environment");
        \Log::info("Current database: $database");
        
        $this->assertEquals('testing', $environment, 'Not using the testing environment!');
        $this->assertEquals('task_management_api_1_test', $database, 'Not using the testing database!');
    }
}