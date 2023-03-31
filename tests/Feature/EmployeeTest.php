<?php

namespace Tests\Feature;

use App\Models\employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_can_create_employee()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);


        $response = $this->actingAs($user)->postJson('/api/employee', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'type' => 'trader',
            'working_hours' => $this->faker->numberBetween(2,10)
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => $response['user']['name'],
            'email' => $response['user']['email'],
        ]);
        $this->assertDatabaseHas('employees', [
            'id' => $response['user']['profile']['id'],
            'type' => $response['user']['profile']['type'],
            'working_hours' => $response['user']['profile']['working_hours']
        ]);
    }

    public function test_can_get_all_employee()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $employee = Employee::factory(10)->create();

        $response = $this->actingAs($user)->getJson('/api/employee');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonFragment([
                'name' => $employee->first()->user->name,
                'email' => $employee->first()->user->email,
            ]);
    }

    public function test_can_update_employee()
    {
        $employee = Employee::factory()->create();

        $response = $this->actingAs($employee->user)->putJson("/api/employee/{$employee->user->id}", [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $response['id'],
            'name' => $response['name'],
            'email' => $response['email'],
        ]);
        $this->assertDatabaseHas('employees', [
            'id' => $response['profile']['id']
        ]);
    }

    public function test_can_delete_employee()
    {

        $employee = Employee::factory()->create();

        $response = $this->actingAs($employee->user)->deleteJson("/api/employee/{$employee->user->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('users', [
            'id' => $employee->user->id,
        ]);
        $this->assertSoftDeleted('employees', [
            'id' => $employee->id
        ]);
    }
}
