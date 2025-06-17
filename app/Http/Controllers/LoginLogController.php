<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Models\LogoutLog;
use App\Services\StudentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginLogController extends Controller
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
                LoginLog::create([
                    'rfid_no' => $rfid_no,
                    'time' => $currentTime,
                    'date' => $currentDate,
                ]);
            });

            $studentDetails = $this->getStudentDetails($rfid_no, 'login');

            return response()->json([
                'valid' => true,
                'success' => 'Login recorded successfully',
                'time' => $timeNow->format('h:i A'),
                'date' => $currentDate,
                'rfid_no' => $rfid_no,
                'log_type' => 'login',
                'fullName' => $studentDetails['fullName'] ?? null,
                'student_lrn' => $studentDetails['student_lrn'] ?? null,
                'image' => $studentDetails['image'] ?? null,
                'timeInOut' => $studentDetails['timeInOut'] ?? null,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to record login: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'error' => 'Failed to record login. Please try again.',
                'rfid_no' => $rfid_no
            ], 500);
        }
    }

    public function getStudentByRFID($rfid_no)
    {
        return response()->json($this->getStudentDetails($rfid_no, 'login'));
    }

    public function recentLogs(Request $request)
    {
        $rfid_no = $request->query('rfid_no');

        if (!$rfid_no) {
            return response()->json(['logs' => []], 200);
        }

        // Get the most recent login and logout logs for the specific RFID
        $latestLogin = LoginLog::where('rfid_no', $rfid_no)
            ->latest('time')
            ->first();

        $latestLogout = LogoutLog::where('rfid_no', $rfid_no)
            ->latest('time')
            ->first();

        $latestLog = null;

        // Determine which log is more recent
        if ($latestLogin && $latestLogout) {
            $latestLog = $latestLogin->time > $latestLogout->time ? $latestLogin : $latestLogout;
        } elseif ($latestLogin) {
            $latestLog = $latestLogin;
        } elseif ($latestLogout) {
            $latestLog = $latestLogout;
        }

        if (!$latestLog) {
            return response()->json(['logs' => []], 200);
        }

        // Prepare the response
        $logType = $latestLog instanceof LoginLog ? 'login' : 'logout';
        $student = $this->getStudentDetails($latestLog->rfid_no, $logType);

        if (!$student['valid']) {
            return response()->json(['logs' => []], 200);
        }

        $log = array_merge($student, [
            'time' => Carbon::parse($latestLog->time)->format('h:i A'),
            'log_type' => $logType,
            'rfid_no' => $latestLog->rfid_no,
        ]);

        return response()->json(['logs' => [$log]], 200);
    }
}
