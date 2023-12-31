<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ApiControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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

    /* USER */
    public function test_users_status()
    {
        $response = $this->get('/api/user/');

        $response->assertStatus(200);
    }

    /**
     * @depends test_users_status
     */
    public function test_users_is_json_response()
    {
        $response = $this->get('/api/user/');

        $response->assertHeader('Content-Type', 'application/json');
    }

    /* TOP DOMAINS */
    public function test_top_domains_status()
    {
        $response = $this->get('/api/user/top-domains');

        $response->assertStatus(200);
    }

    /**
     * @depends test_top_domains_status
     */
    public function test_top_domains_is_json_response()
    {
        $response = $this->get('/api/user/top-domains');

        $response->assertHeader('Content-Type', 'application/json');
    }

    /**
     * @depends test_top_domains_is_json_response
     */
    public function test_top_domains_return_at_least_three_domains()
    {
        $response = $this->get('/api/user/top-domains');

        // Get json response
        $responseData = json_decode($response->getContent(), true);

        $this->assertGreaterThanOrEqual(3, count($responseData));
    }

    /* SHOW USER */
    public function test_can_get_user_by_id()
    {
        $user = User::factory()->create();

        $response = $this->get('/api/user/' . $user->id);

        $response->assertStatus(200);

        // Verify that the response is in JSON format.
        $response->assertJsonStructure(['user' => ['id', 'name', 'email']]);

        // Verify that the user data matches the response data.
        $response->assertJson(['user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]]);
    }

    /* CREATE USER */
    public function test_user_create_validation()
    {
        $data = [
            'name' => 'TestUser',
            'email' => 'testuser@clicko.es',
            'password' => 'password123',
        ];

        $response = $this->json('POST', '/api/user/create', $data);

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

        $response = $this->json('POST', '/api/user/create', $data);

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
            'status' => 0,
            'message' => 'Validation error',
            'errors' => [
                'name' => ['The name field is required.'],
                'email' => ['The email must be a valid email address.'],
                'password' => ['The password must be at least 8 characters.'],
            ],
        ]);
    }

    public function test_create_user_with_duplicate_email()
    {
        $existingUser = User::factory()->create();

        $newUserData = [
            'name' => 'NewUSer',
            'email' => $existingUser->email, // Using equal email
            'password' => 'randompassword123'
        ];

        // Make a POST request to CREATE the new user
        $response = $this->post("/api/user/create", $newUserData);

        $response->assertStatus(422);

        // Verify if response has error msg
        $response->assertJson([
            'status' => 0,
            'message' => 'Validation error',
            'errors' => [
                'email' => ['The email has already been taken.'],
            ],
        ]);
    }

    /* UPDATE USER */
    public function test_update_user_successfully()
    {
        $user = User::factory()->create();

        $newName = $this->faker->name;
        $newEmail = $this->faker->unique()->safeEmail;

        // Make a POST request to update the user
        $response = $this->post("/api/user/edit/{$user->id}", [
            'name' => $newName,
            'email' => $newEmail,
        ]);

        $response->assertStatus(200);

        // Verify 200 successfully message
        $response->assertJson([
            'status' => 1,
            'message' => 'User successfully updated',
        ]);

        // Verify that user data has been correctly updated in the database
        $updatedUser = User::find($user->id);
        $this->assertEquals($newName, $updatedUser->name);
        $this->assertEquals($newEmail, $updatedUser->email);
    }

    public function test_update_nonexistent_user()
    {
        // Try to update a user that does not exist
        $response = $this->post("/api/user/edit/9999", [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
        ]);

        $response->assertStatus(404);

        //  Verify error message
        $response->assertJson([
            'status' => 0,
            'message' => 'Ups! User not found',
        ]);
    }

    public function test_update_user_with_duplicate_email()
    {
        $existingUser = User::factory()->create();

        $newUserData = [
            'name' => 'newUser',
            'email' => $existingUser->email, // Using equal email
        ];

        // Make a POST request to edit the new user
        $response = $this->post("/api/user/edit/{$existingUser->id}", $newUserData);

        $response->assertStatus(422);

        // Verify if response has error msg
        $response->assertJson([
            'status' => 0,
            'message' => 'Validation error',
            'errors' => [
                'email' => ['The email has already been taken.'],
            ],
        ]);
    }

    /* DELETE */
    /**
     * @depends test_users_status
     * @depends test_can_get_user_by_id
     */
    public function test_can_delete_user()
    {
        $user = User::factory()->create();

        $this->delete('/api/user/' . $user->id);

        // Verify that the user has removed from the db
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
