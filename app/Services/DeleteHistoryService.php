<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class DeleteHistoryService extends BaseService
{
    public function deleteMessage(string $messageId): bool
    {
        $contentLength = strlen($messageId) + 64;

        $headers = array_merge($this->commonHeaders, [
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Content-Length' => $contentLength,
            'Origin' => $this->baseUrl,
        ]);

        $body = [
            'isTest' => 'false',
            'goformId' => 'DELETE_SMS',
            'msg_id' => $messageId,
            'notCallback' => 'true',
        ];

        try {
            $response = $this->client->post('/goform/goform_set_cmd_process', [
                'headers' => $headers,
                'form_params' => $body,
            ]);

            $responseData = json_decode($response->getBody(), true);
            Log::info('Delete response: ' . json_encode($responseData));
            return true;
        } catch (RequestException $e) {
            Log::error('Delete message failed: ' . $e->getMessage());
            return false;
        }
    }
}
