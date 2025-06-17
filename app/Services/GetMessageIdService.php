<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class GetMessageIdService extends BaseService
{
    public function getMessageId(): string
    {
        $url = '/goform/goform_get_cmd_process?isTest=false&cmd=sms_data_total&page=0&data_per_page=500&mem_store=1&tags=10&order_by=order+by+id+desc&_=1668786454530';

        try {
            $response = $this->client->get($url, [
                'headers' => $this->commonHeaders,
            ]);

            $data = json_decode($response->getBody(), true);
            $messages = $data['messages'] ?? [];

            if (count($messages) < 5) {
                Log::warning('Less than 5 messages found, exiting.');
                return '';
                // exit(0);
            }

            $msgIds = array_map(fn($msg) => $msg['id'], $messages);
            return implode(';', $msgIds);
        } catch (RequestException $e) {
            Log::error('Get Message ID failed: ' . $e->getMessage());
            return '';
        }
    }
}
