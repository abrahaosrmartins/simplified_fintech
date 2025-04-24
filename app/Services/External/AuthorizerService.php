<?php

namespace App\Services\External;

use App\Services\External\Contracts\AuthorizerServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

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
        while ($retries < 3 ) {
            try {
                $response = $this->client->request('GET', 'v2/authorize');
                $data = json_decode($response->getBody()->getContents(), true);

                return $data['data']['authorization'] ?? false;
            } catch (RequestException $e) {
                $retries++;
                if ($retries >= 3) {
                    Log::error("Falha ao tentar acessar o serviço de autenticação", ['exception' => $e]);
                    return false;
                }
                sleep($retries);
            }
        }
        return false;
    }
}
