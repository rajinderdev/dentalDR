<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppointmentService;
use App\Services\WaitingAreaPatientService;
use App\Services\TreatmentService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    use ApiResponse;

    protected AppointmentService $appointmentService;
    protected TreatmentService $treatmentService;
    protected WaitingAreaPatientService $waitingAreaPatientService;

    public function __construct(
        AppointmentService $appointmentService,
        TreatmentService $treatmentService,
        WaitingAreaPatientService $waitingAreaPatientService
    ) {
        $this->appointmentService = $appointmentService;
        $this->treatmentService = $treatmentService;
        $this->waitingAreaPatientService = $waitingAreaPatientService;
    }

    /**
     * @group Dashboard
     *
     * @method GET
     *
     * List all dashboard
     *
     * @get /
     *
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $doctor = $request->query('doctor', null);
        $doctorIds = is_array($doctor) ? $doctor : (is_null($doctor) ? [] : explode(',', $doctor));
        $providerId = $request->query('providerId', null);
        try {
            $appointments = $this->appointmentService->getAppointmentsForToday($doctorIds,$providerId);
            $waitingArea = $this->waitingAreaPatientService->getWaitingAreaForToday($doctorIds,$providerId);
            $ongoingTreatments = $this->treatmentService->getOngoingTreatmentsForToday($doctorIds,$providerId);
            $completedTreatments = $this->treatmentService->getCompletedTreatmentsForToday($doctorIds,$providerId);

            return $this->successResponse([
                'appointments' => $appointments,
                'waitingArea' => $waitingArea,
                'ongoingTreatments' => $ongoingTreatments,
                'completedTreatments' => $completedTreatments,
                'message' => 'Dashboard Data',
            ]);
        } catch (Exception $e) {
            dd($e);
            return $this->errorResponse();
        }
    }

    /**
     * @group Dashboard
     *
     * @method GET
     *
     * Appointments dashboard
     *
     * @get /
     *
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function appointments(Request $request)
    {
        try {
            $appointments = $this->appointmentService->getAppointmentsForToday();

            return $this->successResponse(['appointments' => $appointments, 'message' => 'Appointment List']);
        } catch (Exception $e) {
            return $this->errorResponse();
        }
    }

    /**
     * @group Dashboard
     *
     * @method GET
     *
     * WaitingArea dashboard
     *
     * @get /
     *
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function waitingArea(Request $request)
    {
        try {
            // Attempt to fetch the patient list from the service
            $waitingAreaList = $this->waitingAreaPatientService->getWaitingAreaForToday();

            // Return the patient list in a successful response
            return $this->successResponse(['waitingArea' => $waitingAreaList, 'message' => 'Waiting Area List']);
        } catch (Exception $e) {
            // Catch any exception and return a generic error message
            return $this->errorResponse(['message' => 'Something went wrong. Please try again later.']);
        }
    }

    /**
     * @group Dashboard
     *
     * @method GET
     *
     * Treatments dashboard
     *
     * @get /
     *
     * @response 500 {"message": "Internal server error"}
     *
     * @return \Illuminate\Http\Response
     */
    public function treatments(Request $request, $status)
    {
        try {
            if ($status == 'ongoing') {
                $treatments = $this->treatmentService->getOngoingTreatmentsForToday();
            } else {
                $treatments = $this->treatmentService->getCompletedTreatmentsForToday();
            }

            return $this->successResponse(['treatments' => $treatments]);
        } catch (Exception $e) {
            return $this->errorResponse();
        }
    }
     public function dashboard()
    {
        Gate::authorize('viewAny-dashboard');

        return view('admin.dashboard');
    }

}
