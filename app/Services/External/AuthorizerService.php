<?php

namespace App\Services\External;

use App\Services\External\Contracts\AuthorizerServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;

class AuthorizerService implements AuthorizerServiceInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://util.devi.tools/api/',
            'timeout' => 5.0,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }


    public function authorize(): bool
    {
        $retries = 0;
        do {
            try {
                $response = $this->client->request('GET', 'v2/authorize');
                $data = json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                if ($retries >= 3) {
                    throw $e;
                }
                //TODO: implementar log
                sleep($retries++);
                return false;
            }
        } while (!$response && $retries < 3 );

        return $data['data']['authorization'] ?? false;
    }
}
