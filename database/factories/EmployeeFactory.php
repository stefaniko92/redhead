<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'type' => Employee::TYPE_PROFESSOR,
            'available_hours' => 8
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Employee $employee) {
            $user = User::factory()->create([
                'profile_type' => Employee::class,
                'profile_id' => $employee->id,
            ]);

            $employee->user()->save($user);
        });
    }
}
