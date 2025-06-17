<?php

namespace App\Observers;

use App\Models\LoginLog;
use App\Jobs\SendSMSJob;
use App\Services\StudentService;
use Illuminate\Support\Facades\Log;

class LoginLogObserver
{
    use StudentService;

    public function created(LoginLog $loginLog)
    {
        try {
            $studentDetails = $this->getStudentDetails($loginLog->rfid_no, 'login');
            if ($studentDetails['valid'] && !empty($studentDetails['parent_number'])) {
                dispatch(new SendSMSJob($studentDetails['parent_number'], $studentDetails['message']));
            } else {
                Log::warning("No valid parent number for RFID {$loginLog->rfid_no} on login.");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send SMS for login log {$loginLog->id}: " . $e->getMessage());
        }
    }
}
