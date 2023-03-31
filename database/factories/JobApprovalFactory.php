<?php

namespace Database\Factories;

use App\Models\Approver;
use App\Models\Job;
use App\Models\JobApproval;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobApproval>
 */
class JobApprovalFactory extends Factory
{
    protected $model = JobApproval::class;

    public function definition(): array
    {

        $job = Job::factory()->create();
        $approver = Approver::factory()->create();

        return [
            'job_id' => $job->id,
            'approver_id' => $approver->user->id,
            'vote' => JobApproval::VOTE_APPROVED,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (JobApproval $jobApproval) {
            $job = $jobApproval->job;
            $job->approved = true;
            $job->save();
        });
    }

    public function numApprovals($num)
    {
        return $this->state(function (array $attributes) use ($num) {
            return [
                'job_id' => function () use ($attributes) {
                    return $attributes['job_id'] ?? Job::factory()->create()->id;
                },
                'approver_id' => function () {
                    return Approver::factory()->create()->user->id;
                },
            ];
        })->count($num);
    }
}
