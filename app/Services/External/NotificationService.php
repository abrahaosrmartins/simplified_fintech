<?php

namespace App\Services\External;

use App\Services\External\Contracts\NotificationServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class NotificationService implements NotificationServiceInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://util.devi.tools/api/',
            'timeout' => 5.0,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function notify(int $userId): void
    {
        $retries = 0;
        while ($retries < 3 ) {
            try {
                $response = $this->client->request('POST', 'v1/notify', [
                    'json' => [$userId],
                ]);

                json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                $retries++;
                if ($retries >= 3) {
                    Log::error("Falha ao tentar enviar notificação.", ['exception' => $e]);
                }
                sleep($retries);
            }
        }
    }
}
