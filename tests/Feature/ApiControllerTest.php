<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ApiControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);
    }

    /* USER */
    public function test_users_status()
    {
        $response = $this->get('/api/users');

        $response->assertStatus(200);
    }

    /**
     * @depends test_users_status
     */
    public function test_users_is_json_response()
    {
        $response = $this->get('/api/users');

        $response->assertHeader('Content-Type', 'application/json');
    }

    /* TOP DOMAINS */
    public function test_top_domains_status()
    {
        $response = $this->get('/api/top-domains');

        $response->assertStatus(200);
    }

    /**
     * @depends test_top_domains_status
     */
    public function test_top_domains_is_json_response()
    {
        $response = $this->get('/api/top-domains');

        $response->assertHeader('Content-Type', 'application/json');
    }

    /**
     * @depends test_top_domains_is_json_response
     */
    public function test_top_domains_return_at_least_three_domains()
    {
        $response = $this->get('/api/top-domains');

        // Get json response
        $responseData = json_decode($response->getContent(), true);

        $this->assertGreaterThanOrEqual(3, count($responseData));
    }
}
