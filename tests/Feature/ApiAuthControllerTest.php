<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ApiAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);

        $user = User::factory()->create();

        // Generate a Sanctum token and assign it to the user
        $token = $user->createToken('auth_token');

        $this->actingAs($user); // Global auth

        // Set the token on the user instance to make it available in all tests
        $user->currentAccessToken = $token;
    }

    public function test_users_status()
    {
        $response = $this->get('/api/user/');

        $response->assertStatus(200);
    }

    /* REGISTER */
    /**
     * @depends test_users_status
     */
    public function test_user_registration_and_token()
    {
        $userData = [
            'name' => 'TestUserName9939393939',
            'email' => 'TestUserName9939393939@clicko.es',
            'password' => 'password123',
        ];

        $response = $this->post('/api/register', $userData);

        // Verify that the response includes the following data
        $response->assertJsonStructure([
            'status',
            'data' => [
                'name',
                'email',
                'updated_at',
                'created_at',
                'id',
            ],
            'acces_token',
            'token_type',
        ]);

        // Check that the user has been created in the db
        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    /* LOGIN */
    /**
     * @depends test_users_status
     */
    public function test_user_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('/api/login', $loginData);

        // Verify that the response includes a success message
        $response->assertJson([
            'status' => 1,
            'message' => "Welcome $user->name!",
        ]);

        // Verify if user is authenticated
        $this->assertAuthenticated();
    }

    /* LOG OUT */
    /**
     * @depends test_users_status
     */
    public function test_user_logout()
    {
        $response = $this->get('/api/logout');

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'You have successfully logged out and the token was successfully deleted',
        ]);
    }
}
