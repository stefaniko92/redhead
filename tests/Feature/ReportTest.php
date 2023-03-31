<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\JobApproval;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }
    /** @test */
    public function earnings_report_ok()
    {
        $numOfApprovals = rand(1,100);
        $jobApproval = JobApproval::factory()
            ->create();
        $job = $jobApproval->job;

        JobApproval::factory()->numApprovals($numOfApprovals)->create([
            'job_id' => $job->id
        ]);

        $response = $this->getJson(route('earning.report'), [
            'job_id' => $jobApproval->job->id,
            'vote' => JobApproval::VOTE_APPROVED,
        ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $jobApproval->job->id,
            'approved' => true
        ]);
        $response->assertStatus(200);

        $response->assertJsonFragment([
            'report' => [
                Carbon::now()->year => [
                    Carbon::now()->month => [
                        Employee::TYPE_PROFESSOR => 6 * ($numOfApprovals + 1),
                        'total_hours' => 6 * ($numOfApprovals + 1)
                    ]
                ]
            ]
        ]);
    }
}
