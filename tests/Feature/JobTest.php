<?php

namespace Tests\Feature;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobTest extends TestCase
{
    use WithFaker;

    public function test_can_create_job(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->actingAs($employee->user)->postJson('/api/jobs', [
            'employee_id' => $employee->user->id,
            'date' => Carbon::tomorrow(),
            'hours' => 5,
        ]);

        $response->assertStatus(201);

        $response->assertJsonFragment([
            'employee_id' => $employee->user->id,
            'hours' => 5
        ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $response['id'],
            'employee_id' => $response['employee_id'],
            'hours' => $response['hours']
        ]);
    }

    public function test_can_not_create_job_with_exceed_hours(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->actingAs($employee->user)->postJson('/api/jobs', [
            'employee_id' => $employee->user->id,
            'date' => Carbon::tomorrow(),
            'hours' => 10,
        ]);

        $response->assertStatus(400);
    }
}
