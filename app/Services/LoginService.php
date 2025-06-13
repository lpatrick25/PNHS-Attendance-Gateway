<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class LoginService extends BaseService
{
    public function initLogin(string $username, string $password): bool
    {
        $user = base64_encode($username);
        $pass = base64_encode($password);

        $headers = array_merge($this->commonHeaders, [
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Content-Length' => '73',
            'Origin' => $this->baseUrl,
        ]);

        $data = [
            'isTest' => 'false',
            'goformId' => 'LOGIN',
            'username' => $user,
            'password' => $pass,
        ];

        try {
            $response = $this->client->post('/goform/goform_set_cmd_process', [
                'headers' => $headers,
                'form_params' => $data,
            ]);

            $responseData = json_decode($response->getBody(), true);
            return $responseData['result'] == 0;
        } catch (RequestException $e) {
            Log::error('Login failed: ' . $e->getMessage());
            return false;
        }
    }
}
