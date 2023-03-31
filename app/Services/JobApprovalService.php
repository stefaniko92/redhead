<?php

namespace App\Services;
use App\Models\Approver;
use App\Models\Job;
use App\Models\JobApproval;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JobApprovalService
{

    public function checkExistingVote($jobID, $approverID)
    {
        $existingJob = JobApproval::where('id', $jobID)
            ->where('approver_id', $approverID)
            ->count();

        return $existingJob > 0;
    }

    /**
     * @param $id
     * @param $approverID
     * @param $vote
     * @return Model|string
     */
    public function vote($id, $approverID, $vote) : Model|string
    {
        DB::beginTransaction();
        try {
            $job = Job::find($id);

            if(!$job) {
                throw new NotFoundHttpException("Not found");
            }

            JobApproval::create([
                'job_id' => $job->id,
                'approver_id' => $approverID,
                'vote' => $vote
            ]);
            DB::commit();

            if(JobApproval::where('job_id', $job->id)
                ->where('vote', JobApproval::VOTE_REJECTED)->count() > 0) {
                $job->approved = false;
                $job->save();

                return $job;
            }

            $jobApprovalsCount = JobApproval::where('job_id', $job->id)
                ->where('approver_id', $approverID)
                ->where('vote', JobApproval::VOTE_APPROVED)
                ->count();

            $approversCount = User::where('profile_type', Approver::class)
                ->count();

            if($jobApprovalsCount >= $approversCount) {
                $job->approved = true;
                $job->save();
            }


        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $job;
    }

}
