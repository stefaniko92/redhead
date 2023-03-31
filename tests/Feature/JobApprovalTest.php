<?php

namespace Tests\Feature;

use App\Models\Approver;
use App\Models\Employee;
use App\Models\Job;
use App\Models\JobApproval;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class JobApprovalTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $approver = Approver::factory()->create();
        $this->actingAs($approver->user);
    }

    /** @test */
    public function an_approver_can_approve_a_job()
    {
        $job = Job::factory()->create();

        $this->postJson(route('jobs.vote'), [
            'job_id' => $job->id,
            'vote' => JobApproval::VOTE_APPROVED,
        ])
            ->assertStatus(201);

        $this->assertDatabaseHas('job_approvals', [
            'job_id' => $job->id,
            'approver_id' => auth()->user()->id,
            'vote' => 'APPROVED',
        ]);
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'approved' => true
        ]);
    }

    /** @test */
    public function an_approver_can_reject_a_job()
    {
        $job = Job::factory()->create();

        $this->postJson(route('jobs.vote'), [
            'job_id' => $job->id,
            'vote' => 'REJECTED',
        ])
            ->assertStatus(201);

        $this->assertDatabaseHas('job_approvals', [
            'job_id' => $job->id,
            'approver_id' => auth()->user()->id,
            'vote' => 'REJECTED',
        ]);
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'approved' => false
        ]);
    }

    /** @test */
    public function an_approver_can_only_vote_on_a_job_once()
    {
        $job = Job::factory()->create();

        $this->postJson(route('jobs.vote'), [
            'job_id' => $job->id,
            'approver_id' => auth()->user()->id,
            'vote' => 'APPROVED',
        ])
            ->assertStatus(Response::HTTP_CREATED);

        $this->postJson(route('jobs.vote'), [
            'job_id' => $job->id,
            'vote' => 'REJECTED',
        ])->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertDatabaseHas('job_approvals', [
            'job_id' => $job->id,
            'approver_id' => auth()->user()->id,
            'vote' => 'APPROVED',
        ]);
    }

    /** @test */
    public function a_non_approver_cannot_vote_on_a_job()
    {
        $job = Job::factory()->create();
        $employee = Employee::factory()->create();
        $this->actingAs($employee->user);

        $this->postJson(route('jobs.vote'), [
            'job_id' => $job->id,
            'approver_id' => $employee->user_id,
        ])->assertStatus(403);

    }
}
