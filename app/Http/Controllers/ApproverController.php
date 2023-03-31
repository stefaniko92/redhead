<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproverCreateRequest;
use App\Http\Requests\ApproverUpdateRequest;
use App\Http\Resources\ApproverCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ApproverService;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;

class ApproverController extends Controller
{
    use ApiResponseHelpers;

    /**
     * @var ApproverService $approverService
     */
    protected $approverService;

    public function __construct()
    {
        $this->approverService = new ApproverService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $approvers = $this->approverService->index();

        return $this->respondWithSuccess($approvers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApproverCreateRequest $request)
    {
        $approver = $this->approverService->create($request->all());

        if(!$approver instanceof User) {
            return $this->respondError($approver);
        }

        return $this->respondCreated([
            'user' => new UserResource(new UserResource($approver))
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $approver = $this->approverService->show($id);

        if(!$approver instanceof User) {
            return $this->respondNotFound('User not found');
        }

        return $this->respondWithSuccess(new UserResource($approver));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ApproverUpdateRequest $request, string $id)
    {
        $approver = $this->approverService->update($request->all(), $id);

        if(!$approver instanceof User) {
            return $this->respondError($approver);
        }

        return $this->respondWithSuccess(new UserResource($approver));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = $this->approverService->delete($id);

        if(!is_bool($deleted)) {
            return $this->respondError($deleted);
        }

        return $this->respondNoContent(['message' => 'Deleted successfully']);
    }
}
