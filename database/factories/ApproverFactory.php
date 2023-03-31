<?php

namespace Database\Factories;

use App\Models\Approver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Approver>
 */
class ApproverFactory extends Factory
{
    protected $model = Approver::class;

    public function definition()
    {
        return [

        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Approver $approver) {
            $user = User::factory()->create([
                'profile_type' => Approver::class,
                'profile_id' => $approver->id,
            ]);

            $approver->user()->save($user);
        });
    }
}
