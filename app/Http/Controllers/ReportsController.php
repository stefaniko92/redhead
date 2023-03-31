<?php

namespace App\Http\Controllers;

use App\Services\EarningReportService;
use F9Web\ApiResponseHelpers;

class ReportsController
{
    use ApiResponseHelpers;
    /**
     * @var EarningReportService $earningReportService
     */
    protected $earningReportService;


    public function __construct()
    {
        $this->earningReportService = new EarningReportService();
    }

    public function report()
    {
        $earningReport = $this->earningReportService->getEarningsReport();

        return $this->respondWithSuccess([
            'report' => $earningReport
        ]);
    }
}
