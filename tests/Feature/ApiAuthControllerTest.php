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
        // Datos de prueba para el nuevo usuario
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
}
