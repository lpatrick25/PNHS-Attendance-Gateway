<?php

namespace App\Observers;

use App\Models\LogoutLog;
use App\Jobs\SendSMSJob;
use App\Services\StudentService;
use Illuminate\Support\Facades\Log;

class LogoutLogObserver
{
    use StudentService;

    public function created(LogoutLog $logoutLog)
    {
        try {
            $studentDetails = $this->getStudentDetails($logoutLog->rfid_no, 'logout');
            if ($studentDetails['valid'] && !empty($studentDetails['parent_number'])) {
                dispatch(new SendSMSJob($studentDetails['parent_number'], $studentDetails['message']));
            } else {
                Log::warning("No valid parent number for RFID {$logoutLog->rfid_no} on logout.");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send SMS for logout log {$logoutLog->id}: " . $e->getMessage());
        }
    }
}
