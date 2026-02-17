<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReminderService;

class ReminderController extends Controller
{
    public function __construct(private ReminderService $reminderService) {}

    public function fetchBirthdayReminders(Request $request)
    {
        $date = $request->input('date');
        // $result = $this->reminderService->sendBirthdayReminders();
        $result = $this->reminderService->getBirthday($date);
        return response()->json($result);
    }

    public function fetchCheckupReminders(Request $request)
    {
        $result = $this->reminderService->sendCheckupReminders();
        return response()->json($result);
    }

    public function fetchAllPatientReminders(Request $request)
    {
        $message = $request->input('message');
        if (!$message) {
            return response()->json(['success' => false, 'message' => 'A message is required.'], 400);
        }
        $result = $this->reminderService->sendAllPatientReminders($message);
        return response()->json($result);
    }
}
