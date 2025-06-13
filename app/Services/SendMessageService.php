<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class SendMessageService extends BaseService
{
    public function sendSms(string $mobileNumber, string $message): ?array
    {
        // Convert message to UTF-16LE and hex
        $msgContent = bin2hex(mb_convert_encoding($message, 'UTF-16LE', 'UTF-8'));
        $msgContent = !empty($msgContent) ? '00' . substr($msgContent, 0, -2) : '';

        // Create timestamp
        $now = now();
        $timestamp = sprintf(
            '%s;%02d;%02d;%02d;%02d;%02d;+8',
            $now->format('y'),
            $now->month,
            $now->day,
            $now->hour,
            $now->minute,
            $now->second
        );

        $bodyData = [
            'isTest' => 'false',
            'goformId' => 'SEND_SMS',
            'notCallback' => 'true',
            'Number' => $mobileNumber,
            'sms_time' => $timestamp,
            'MessageBody' => $msgContent,
            'ID' => '-1',
            'encode_type' => 'GSM7_default',
        ];

        // Calculate Content-Length
        $mobileByteSize = strlen($mobileNumber);
        $bodyByteSize = strlen($msgContent);
        $contentLength = ($mobileByteSize + $bodyByteSize + 231) - 88;

        $headers = array_merge($this->commonHeaders, [
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Content-Length' => $contentLength,
            'Origin' => $this->baseUrl,
        ]);

        try {
            $response = $this->client->post('/goform/goform_set_cmd_process', [
                'headers' => $headers,
                'form_params' => $bodyData,
            ]);

            $responseData = json_decode($response->getBody(), true);
            return ['result' => $responseData['result']];
        } catch (RequestException $e) {
            Log::error('Send SMS failed: ' . $e->getMessage());
            return null;
        }
    }
}
