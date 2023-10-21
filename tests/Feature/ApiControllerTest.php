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

    public function test_user_create_validation()
    {
        $data = [
            'name' => 'TestUser',
            'email' => 'testuser@clicko.es',
            'password' => 'password123',
        ];

        $response = $this->json('POST', '/api/create-user', $data);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 1,
                'msg' => 'User created successfully',
            ]);

        // Verify that the user has been created in the database
        $this->assertDatabaseHas('users', [
            'name' => 'TestUser',
            'email' => 'testuser@clicko.es',
        ]);
    }

    public function test_user_create_validation_error()
    {
        $data = [
            'name' => '', // Empty name (its required)
            'email' => 'invalid_email', // Invalid email
            'password' => 'test', // Short password
        ];

        $response = $this->json('POST', '/api/create-user', $data);

        $response->assertStatus(422) // 422 = Unprocessable Entity
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'name',
                    'email',
                    'password',
                ],
            ]);

        // Verify that the error message expected
        $response->assertJson([
            'message' => 'The name field is required. (and 2 more errors)',
            'errors' => [
                'name' => ['The name field is required.'],
                'email' => ['The email must be a valid email address.'],
                'password' => ['The password must be at least 8 characters.'],
            ],
        ]);
    }
}
