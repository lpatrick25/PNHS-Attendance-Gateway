<?php

namespace App\Http\Controllers;

use App\Services\LoginService;
use App\Services\SendMessageService;
use App\Services\GetMessageIdService;
use App\Services\DeleteHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function process(Request $request): JsonResponse
    {
        // Validate request parameters
        $request->validate([
            'phone_number' => 'required|string',
            'message' => 'required|string',
        ]);

        // Login
        $loginService = new LoginService();
        $loginSuccess = $loginService->initLogin('user', '@l03e1t3');

        if (!$loginSuccess) {
            return response()->json(['error' => 'Login failed'], 401);
        }

        // Send SMS
        $sendService = new SendMessageService();
        $smsResponse = $sendService->sendSms($request->input('phone_number'), $request->input('message'));

        if (!$smsResponse) {
            return response()->json(['error' => 'Failed to send SMS'], 500);
        }

        // Get Message IDs
        $getMessageIdService = new GetMessageIdService();
        $messageIds = $getMessageIdService->getMessageId();

        if (empty($messageIds)) {
            // Delete Messages
            $deleteService = new DeleteHistoryService();
            $deleteSuccess = $deleteService->deleteMessage($messageIds);
        }

        return response()->json([
            'login' => $loginSuccess,
            'sms' => $smsResponse,
            'delete' => $deleteSuccess,
        ]);
    }
}
