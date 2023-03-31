<?php

namespace App\Services;

use App\Models\Approver;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApproverService
{

    /**
     * @return LengthAwarePaginator
     */
    public function index() : LengthAwarePaginator
    {
        $users = User::where('profile_type', '=', Approver::class)->paginate(10);

        return $users;
    }

    /**
     * @param $id
     * @return Model|string
     */
    public function show($id) : Model|string
    {
        try {
            $user = User::where('profile_type', '=', Approver::class)
                ->whereId($id)->first();

            if(!$user) {
                 throw new NotFoundHttpException("Not found");
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }


        return $user;
    }

    /**
     * @param $data
     * @return Model|string
     */
    public function create($data) : Model|string
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'email_verified_at' => Carbon::now()
            ]);
            $approver = Approver::create([]);
            $approver->user()->save($user);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return $user;
    }

    /**
     * @param $data
     * @param $id
     * @return Model|string
     */
    public function update($data, $id) : Model|string
    {
        DB::beginTransaction();
        try {
            $user = User::where('profile_type', '=', Approver::class)->whereId($id)->first();

            if(!$user) {
                throw new NotFoundHttpException("Not found");
            }

            $user->update($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return $user;
    }

    /**
     * @param $id
     * @return bool|string
     */
    public function delete($id) : bool|string
    {
        DB::beginTransaction();
        try {
            $user = User::where('profile_type', '=', Approver::class)->whereId($id)->first();

            if(!$user) {
                throw new NotFoundHttpException("Not found");
            }

            $approver = Approver::find($user->profile_id);

            $deletedApprover = $approver->delete();
            $deletedUser = $user->delete();
            if(!($deleted = ($deletedUser && $deletedApprover))) {
                throw new Exception("User cannot be deleted");
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }

        return $deleted;
    }
}
