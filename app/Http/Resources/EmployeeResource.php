<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'type' => $this->hasEmployeeProfile ? User::TYPE_NON_APPROVER : User::TYPE_APPROVER,
            'created_at' => $this->created_at,
            'profile' => $this->profile
        ];
    }
}
