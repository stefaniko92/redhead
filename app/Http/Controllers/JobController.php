<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobCreateRequest;
use App\Models\Job;
use App\Services\JobService;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;

class JobController extends Controller
{
    use ApiResponseHelpers;

    /**
     * @var JobService $jobService
     */
    protected $jobService;

    public function __construct()
    {
        $this->jobService = new JobService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = $this->jobService->index();

        return $this->respondWithSuccess($jobs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobCreateRequest $request)
    {
        $job = $this->jobService->create($request->only('employee_id', 'hours', 'date'));

        if(!$job instanceof Job) {
            return $this->respondError($job);
        }

        return $this->respondCreated($job);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = $this->jobService->show($id);

        if(!$job instanceof Job) {
            return $this->respondError($job);
        }

        return $this->respondWithSuccess($job);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $job = $this->jobService->update($request->only('employee_id', 'hours', 'date'), $id);

        if(!$job instanceof Job) {
            return $this->respondError($job);
        }

        return $this->respondWithSuccess($job);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = $this->jobService->delete($id);

        if(!is_bool($deleted)) {
            return $this->respondError($deleted);
        }

        return $this->respondWithSuccess('Deleted successfully');
    }
}
