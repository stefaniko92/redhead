<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    use RefreshDatabase, WithFaker;

    protected $model = Job::class;

    public function definition()
    {
        $employee = Employee::factory()->create();
        $date = Carbon::now();

        return [
            'employee_id' => $employee->user->id,
            'date' => $date,
            'hours' => 6,
        ];
    }
}
