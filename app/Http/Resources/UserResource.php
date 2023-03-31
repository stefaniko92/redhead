<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile_id' => $this->profile_id,
            'type' => $this->hasApproverProfile ? User::TYPE_APPROVER : User::TYPE_NON_APPROVER,
            'created_at' => $this->created_at,
        ];
    }
}
