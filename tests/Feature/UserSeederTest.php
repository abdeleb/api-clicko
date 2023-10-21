<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_users_table_has_atleast_20_rows()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);

        $count = User::count();
        $this->assertGreaterThanOrEqual(20, $count);
    }

    public function test_users_has_different_email()
    {
        Artisan::call('db:seed', ['--class' => 'UserSeeder']);

        $emailAddresses = \App\Models\User::pluck('email')->toArray();

        // Check that at least one email address is different from the other
        $this->assertNotEquals($emailAddresses, array_fill(0, count($emailAddresses), $emailAddresses[0]));
    }
}
