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
    public function test_users_method_status_and_is_json()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);

        $users = User::all();

        $response = $this->get('/api/users');

        // Verify status
        $response->assertStatus(200);

        // Verify json response
        $response->assertJson($users->toArray());
    }
}
