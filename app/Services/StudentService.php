<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

trait StudentService
{
    protected function getStudentDetails($rfid_no, $logType)
    {
        if (empty($rfid_no) || !is_string($rfid_no)) {
            return ['valid' => false, 'msg' => 'Invalid RFID provided.'];
        }

        $client = new Client(['timeout' => 10]);

        try {
            $apiUrl = env('API_URL', 'http://127.0.0.1:8000/api');
            $response = $client->get("{$apiUrl}/getStudentByRFID/" . urlencode($rfid_no));

            if ($response->getStatusCode() !== 200) {
                return ['valid' => false, 'msg' => 'Failed to fetch student data from API.'];
            }

            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['student_lrn'])) {
                return ['valid' => false, 'msg' => 'Student not found.'];
            }

            $studentImage = $data['image'] ?? 'default.png';
            $studentLrn = $data['student_lrn'];
            $studentName = trim(implode(' ', array_filter([
                $data['first_name'] ?? '',
                $data['middle_name'] ?? '',
                $data['last_name'] ?? ''
            ])));
            $parentContact = $this->formatPhoneNumber($data['parent_contact'] ?? '');

            $timeNow = Carbon::now('Asia/Manila');
            $date = $timeNow->toDateString();
            $time = $timeNow->format('h:i A');

            $message = sprintf(
                "Dear Parent/Guardian,\n\nYour child, %s (LRN: %s), has %s the school premises at %s on %s.\n\nThis is an automated message.",
                $studentName,
                $studentLrn,
                $logType === 'login' ? 'entered' : 'exited',
                $time,
                $date,
            );

            return [
                'valid' => true,
                'date' => $date,
                'time' => $time,
                'image' => $studentImage,
                'student_lrn' => $studentLrn,
                'fullName' => $studentName,
                'parent_number' => $parentContact,
                'message' => $message,
                'timeInOut' => "<span>Time " . ($logType === 'login' ? 'In' : 'Out') . ": <strong class='text-danger'>{$time}</strong></span>",
            ];
        } catch (RequestException $e) {
            Log::error('API request failed: ' . $e->getMessage());
            return ['valid' => false, 'msg' => 'API request error: ' . $e->getMessage()];
        } catch (\Exception $e) {
            Log::error('Unexpected error: ' . $e->getMessage());
            return ['valid' => false, 'msg' => 'Unexpected error occurred.'];
        }
    }

    protected function formatPhoneNumber($number)
    {
        if (empty($number)) {
            return '';
        }

        $formatted = preg_replace('/\D+/', '', $number);
        return (substr($formatted, 0, 2) === '63') ? '0' . substr($formatted, 2) : $formatted;
    }
}
