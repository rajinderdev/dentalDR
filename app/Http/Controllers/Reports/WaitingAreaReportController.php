<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WaitingAreaReportController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->format('Y-m-d');
        $filters = [
            'start_date' => $request->input('start_date', $request->input('startDate', $today)),
            'end_date' => $request->input('end_date', $request->input('endDate', $today)),
            'doctor_id' => $request->input('doctor_id', 'all'),
            'time_shift' => $request->input('time_shift', 'all'),
        ];

        $perPage = (int) $request->input('per_page', 50);

        $service = app(\App\Services\Reports\WaitingAreaReportService::class);
        $result = $service->getWaitingAreaReport($filters, $perPage);

        return response()->json($result);
    }
}
