<?php

namespace App\Http\Controllers;

use App\Models\LogoutLog;
use App\Services\StudentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LogoutLogController extends Controller
{
    use StudentService;

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rfid_no' => 'required|string|max:20|regex:/^[a-zA-Z0-9]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'valid' => false,
                'error' => $validator->errors()->first(),
                'rfid_no' => $request->rfid_no
            ], 422);
        }

        $rfid_no = $request->rfid_no;
        $timeNow = Carbon::now('Asia/Manila');
        $currentDate = $timeNow->toDateString();
        $currentTime = $timeNow->toTimeString();

        try {
            DB::transaction(function () use ($rfid_no, $currentTime, $currentDate) {
                LogoutLog::create([
                    'rfid_no' => $rfid_no,
                    'time' => $currentTime,
                    'date' => $currentDate,
                ]);
            });

            $studentDetails = $this->getStudentDetails($rfid_no, 'logout');

            return response()->json([
                'valid' => true,
                'success' => 'Logout recorded successfully',
                'time' => $timeNow->format('h:i A'),
                'date' => $currentDate,
                'rfid_no' => $rfid_no,
                'log_type' => 'logout',
                'fullName' => $studentDetails['fullName'] ?? null,
                'student_lrn' => $studentDetails['student_lrn'] ?? null,
                'image' => $studentDetails['image'] ?? null,
                'timeInOut' => $studentDetails['timeInOut'] ?? null,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to record logout: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'error' => 'Failed to record logout. Please try again.',
                'rfid_no' => $rfid_no
            ], 500);
        }
    }

    public function getStudentByRFID($rfid_no)
    {
        return response()->json($this->getStudentDetails($rfid_no, 'logout'));
    }
}
