<?php

namespace App\Services;

use GuzzleHttp\Client;

abstract class BaseService
{
    protected $client;
    protected $baseUrl = 'http://192.168.254.254';
    protected $commonHeaders = [
        'Accept' => 'application/json, text/javascript, */*; q=0.01',
        'Accept-Encoding' => 'gzip, deflate',
        'Accept-Language' => 'en-US,en;q=0.9',
        'Connection' => 'keep-alive',
        'DNT' => '1',
        'Host' => '192.168.254.254',
        'Referer' => 'http://192.168.254.254/index.html?t=',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
        'X-Requested-With' => 'XMLHttpRequest',
        'Cookie' => 'pageForward=home',
    ];

    public function __construct()
    {
        $this->client = new Client(['base_uri' => $this->baseUrl]);
    }
}
