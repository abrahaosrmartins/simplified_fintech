<?php

namespace App\Services\External;

use App\Services\External\Contracts\NotificationServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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

    public function notify(int $userId): bool
    {
        $retries = 0;
        do {
            try {
                $response = $this->client->request('POST', 'v1/notify', [
                    'json' => [$userId],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);
            } catch (RequestException $e) {
                if ($retries >= 3) {
                    throw $e;
                }
                //TODO: implementar log
                sleep($retries++);
                return false;
            }
            return false;
        } while (!$response && $retries < 3 );

        return $data['status'] === 'success';
    }
}
