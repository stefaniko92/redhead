<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('password')
        ]);
    }

    public function test_show_validation_error_when_both_fields_empty()
    {

        $response = $this->json('POST', route('auth.login'), [
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }


    public function test_show_validation_error_on_email_when_credential_donot_match()
    {
        $response = $this->json('POST', route('auth.login'), [
            'email' => 'test@test.com',
            'password' => 'abcdabcd'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_return_user_and_access_token_after_successful_login()
    {
        $response = $this->json('POST', route('auth.login'), [
            'email' =>'johndoe@example.com',
            'password' => 'password',
            'device_name' => 'Android'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_non_authenticated_user_cannot_logout()
    {
        $response = $this->json('POST', route('auth.logout'), []);

        $response->assertStatus(401)
            ->assertSee('Unauthenticated');;
    }

    public function test_authenticated_user_can_logout()
    {
        Sanctum::actingAs(
            User::first(),
        );

        $response = $this->json('POST', route('auth.logout'), []);

        $response->assertStatus(200);
    }
}
