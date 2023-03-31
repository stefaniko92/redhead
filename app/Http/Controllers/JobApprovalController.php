<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteRequest;
use App\Services\JobApprovalService;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobApprovalController extends Controller
{
    use ApiResponseHelpers;
    /**
     * @var JobApprovalService $jobApprovalService
     */
    protected $jobApprovalService;

    public function __construct()
    {
        $this->jobApprovalService = new JobApprovalService();
    }

    /**
     * @param VoteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function vote(VoteRequest $request) : JsonResponse
    {
        $existingVote = $this->jobApprovalService
            ->checkExistingVote($request->get('job_id'), auth()->user()->id);

        if($existingVote) {
            return $this->respondFailedValidation('Your vote already exists');
        }
        $vote = $this->jobApprovalService->vote($request->get('job_id'),
            auth()->user()->id,
            $request->get('vote'));

        return $this->respondCreated($vote);
    }
}
