<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EarningReportService
{
    /**
     * @return array
     */
    public function getEarningsReport()
    {
        $firstYear = DB::table('jobs')->selectRaw('YEAR(MIN(date)) as first_year')->value('first_year');
        $lastYear = DB::table('jobs')->selectRaw('YEAR(MAX(date)) as last_year')->value('last_year');

        $earnings = [];

        for ($year = $firstYear; $year <= $lastYear; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                // Get the start and end date for the current month and year
                $startDate = Carbon::create($year, $month, 1);
                $endDate = $startDate->copy()->endOfMonth();

                $approvedJobs = DB::table('jobs')
                    ->join('job_approvals', 'jobs.id', '=', 'job_approvals.job_id')
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('approved', 1)
                    ->groupBy('jobs.employee_id')
                    ->get(['jobs.employee_id', DB::raw('SUM(jobs.hours) as total_hours')]);

                // Calculate the earnings for each employee
                foreach ($approvedJobs as $job) {
                    $employee = User::find($job->employee_id);
                    isset($earnings[$year][$month][$employee->profile->type]) ?
                        $earnings[$year][$month][$employee->profile->type] += (float)$job->total_hours :
                        $earnings[$year][$month][$employee->profile->type] = (float)$job->total_hours;

                    isset($earnings[$year][$month]['total_hours']) ? $earnings[$year][$month]['total_hours'] += $job->total_hours :
                        $earnings[$year][$month]['total_hours'] = (float)$job->total_hours;
                }
            }
        }

        return $earnings;
    }
}
