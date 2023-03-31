<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeCreateRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\EmployeeService;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use ApiResponseHelpers;

    /**
     * @var EmployeeService $employeeService
     */
    protected $employeeService;

    public function __construct()
    {
        $this->employeeService = new EmployeeService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employee = $this->employeeService->index();

        return $this->respondWithSuccess($employee);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeCreateRequest $request)
    {
        $user = $this->employeeService->create($request->all());

        if(!$user instanceof User) {
            return $this->respondError($user);
        }

        return $this->respondCreated([
            'user' => new EmployeeResource($user)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = $this->employeeService->show($id);

        if(!$employee instanceof User) {
            return $this->respondNotFound('User not found');
        }

        return $this->respondWithSuccess(new EmployeeResource($employee));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = $this->employeeService->update(
            $request->only('email', 'name'),
            $request->only('type', 'working_hours', 'available_hours'),
            $id
        );

        if(!$employee instanceof User) {
            return $this->respondError($employee);
        }

        return $this->respondWithSuccess(new EmployeeResource($employee));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deleted = $this->employeeService->delete($id);

        if(!is_bool($deleted)) {
            return $this->respondError($deleted);
        }

        return $this->respondNoContent(['message' => 'Deleted successfully']);
    }
}
