<?php

namespace Tests\Feature;

use App\Models\Approver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApproverTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_can_create_approver()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);


        $response = $this->actingAs($user)->postJson('/api/approvers', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password'
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => $response['user']['name'],
            'email' => $response['user']['email'],
        ]);
        $this->assertDatabaseHas('approvers', [
            'id' => $response['user']['profile_id']
        ]);
    }

    public function test_can_get_all_approvers()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $approvers = Approver::factory(10)->create();

        $response = $this->actingAs($user)->getJson('/api/approvers');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonFragment([
                'name' => $approvers->first()->user->name,
                'email' => $approvers->first()->user->email,
            ]);
    }

    public function test_can_update_approver()
    {
        $approver = Approver::factory()->create();

        $response = $this->actingAs($approver->user)->putJson("/api/approvers/{$approver->user->id}", [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $response['id'],
            'name' => $response['name'],
            'email' => $response['email'],
        ]);
        $this->assertDatabaseHas('approvers', [
            'id' => $response['profile_id']
        ]);
    }

    public function test_can_delete_approver()
    {

        $approver = Approver::factory()->create();

        $response = $this->actingAs($approver->user)->deleteJson("/api/approvers/{$approver->user->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('users', [
            'id' => $approver->user->id,
        ]);
        $this->assertSoftDeleted('approvers', [
            'id' => 1
        ]);
    }
}
