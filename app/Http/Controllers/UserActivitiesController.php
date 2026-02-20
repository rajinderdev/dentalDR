<?php

namespace App\Http\Controllers;

use App\Models\EcgActivityEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables as YajraDataTables;

class UserActivitiesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = EcgActivityEvent::leftJoin('Patient', 'Ecg_Activity_Events.PatientID', '=', 'Patient.PatientID')
                ->select([
                    'Ecg_Activity_Events.EventActivityID',
                    'Ecg_Activity_Events.CreatedOn',
                    'Ecg_Activity_Events.CreatedBy',
                    'Ecg_Activity_Events.EventTypeName',
                    'Ecg_Activity_Events.EventDetails',
                    'Ecg_Activity_Events.PatientID',
                    DB::raw("CONCAT(COALESCE(Patient.Title, ''), ' ', COALESCE(Patient.FirstName, ''), ' ', COALESCE(Patient.LastName, '')) as PatientName"),
                ])
                ->where(function ($q) {
                    $q->where('Ecg_Activity_Events.Isdeleted', false)
                      ->orWhereNull('Ecg_Activity_Events.Isdeleted');
                });

            return YajraDataTables::of($query)
                ->filter(function ($query) use ($request) {
                    // Text search
                    if ($request->has('search') && $request->get('search') != '') {
                        $searchTerm = '%' . $request->get('search') . '%';
                        $query->where(function ($q) use ($searchTerm) {
                            $q->where('Ecg_Activity_Events.EventTypeName', 'like', $searchTerm)
                              ->orWhere('Ecg_Activity_Events.EventDetails', 'like', $searchTerm)
                              ->orWhere('Ecg_Activity_Events.CreatedBy', 'like', $searchTerm)
                              ->orWhere('Patient.FirstName', 'like', $searchTerm)
                              ->orWhere('Patient.LastName', 'like', $searchTerm);
                        });
                    }

                    // Date range filter
                    if ($request->get('date_filter') == '1') {
                        if ($request->has('start_date') && $request->get('start_date') != '') {
                            $query->whereDate('Ecg_Activity_Events.CreatedOn', '>=', $request->get('start_date'));
                        }
                        if ($request->has('end_date') && $request->get('end_date') != '') {
                            $query->whereDate('Ecg_Activity_Events.CreatedOn', '<=', $request->get('end_date'));
                        }
                    }
                })
                ->editColumn('CreatedOn', function ($event) {
                    return $event->CreatedOn ? $event->CreatedOn->format('m/d/Y \a\t g:i A') : 'N/A';
                })
                ->editColumn('PatientName', function ($event) {
                    return trim($event->PatientName) ?: '-';
                })
                ->editColumn('CreatedBy', function ($event) {
                    return $event->CreatedBy ?: '-';
                })
                ->orderColumn('CreatedOn', function ($query, $direction) {
                    $query->orderBy('Ecg_Activity_Events.CreatedOn', $direction);
                })
                ->make(true);
        }

        return view('admin.user-activities.index');
    }
}
