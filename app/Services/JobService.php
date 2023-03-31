<?php

namespace App\Services;

use App\Exceptions\ExceedHoursException;
use App\Models\Employee;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JobService
{
    /**
     * @return LengthAwarePaginator
     */
    public function index() : LengthAwarePaginator
    {
        $jobs = Job::paginate(10);

        return $jobs;
    }

    /**
     * @param $id
     * @return Model|string
     */
    public function show($id) : Model|string
    {
        try {
            $job = Job::find($id);

            if(!$job) {
                 throw new NotFoundHttpException("Not found");
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $job;
    }

    /**
     * @param $data
     * @return Model|string
     */
    public function create($data) : Model|string
    {
        DB::beginTransaction();
        try {
            $employee = User::find($data['employee_id']);

            if(!$employee instanceof User) {
                 throw new NotFoundHttpException("Not found");
            }
            if(($employee->profile->type === Employee::TYPE_TRADER && $employee->working_hours < (int)$data['hours']) ||
                ($employee->profile->type === Employee::TYPE_PROFESSOR && $employee->available_hours < (int)$data['hours'])) {
                throw new ExceedHoursException("Job exceed maximum number of hours");
            }

            $job = Job::create([
                'employee_id' => $data['employee_id'],
                'date' => $data['date'],
                'hours' => $data['hours']
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        } catch (ExceedHoursException $exceedHoursException) {
            return $exceedHoursException->getMessage();
        }

        return $job;
    }

    /**
     * @param $data
     * @param $id
     * @return Model|string
     */
    public function update($data, $id): Model|string
    {
        DB::beginTransaction();
        try {
            $job = Job::find($id);

            if(!$job) {
                 throw new NotFoundHttpException("Not found");
            }

            $employee = User::find($data['employee_id']);

            if(!$employee instanceof User) {
                 throw new NotFoundHttpException("Not found");
            }
            if($job->employee_id !== $data['employee_id'] || $job->hours !== $data['hours']) {
                if(($employee->profile->type === Employee::TYPE_TRADER && $employee->working_hours < (int)$data['hours']) ||
                    ($employee->profile->type === Employee::TYPE_PROFESSOR && $employee->available_hours < (int)$data['hours'])) {
                    throw new ExceedHoursException("Job exceed maximum number of hours");
                }
            }

            $job->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        } catch (ExceedHoursException $exceedHoursException) {
            return $exceedHoursException->getMessage();
        }

        return $job;
    }

    /**
     * @param $id
     * @return bool|string
     */
    public function delete($id) : bool|string
    {
        DB::beginTransaction();
        try {
            $job = Job::find($id);

            if(!$job) {
                 throw new NotFoundHttpException("Not found");
            }

            $deleted = $job->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return $deleted;
    }
}
