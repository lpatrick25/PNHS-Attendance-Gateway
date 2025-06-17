<?php

namespace App\Jobs;

use App\Services\SendMessageService;
use App\Services\GetMessageIdService;
use App\Services\DeleteHistoryService;
use App\Services\LoginService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phoneNumber;
    protected $message;

    public function __construct($phoneNumber, $message)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
    }

    public function handle()
    {
        $loginService = new LoginService();
        if (!$loginService->initLogin('user', '@l03e1t3')) {
            Log::warning('SMS login failed in job');
            return;
        }

        $sendService = new SendMessageService();
        if (!$sendService->sendSms($this->phoneNumber, $this->message)) {
            Log::warning('Failed to send SMS to ' . $this->phoneNumber);
        }

        $getMessageIdService = new GetMessageIdService();
        $messageIds = $getMessageIdService->getMessageId();
        if (!empty($messageIds)) {
            $deleteService = new DeleteHistoryService();
            $deleteService->deleteMessage($messageIds);
        }
    }
}
