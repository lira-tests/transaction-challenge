<?php

namespace App\Services;

use App\Exceptions\SendNotifyFailException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    const SERVICE_URL = 'https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04';

    const SENT = 'Enviado';

    public function send(array $message): bool
    {
        $response = $this->process($message);

        if (!$response) {
            throw new SendNotifyFailException();
        }

        return true;
    }

    private function process(array $message) : bool
    {
        $response = $this->request($message);

        $body = $response->json();

        return $this->isSent($response, $body);
    }

    private function request(array $message)
    {
        return Http::post(self::SERVICE_URL, $message);
    }

    private function isSent($response, $body) : bool
    {
        return (
            $response->status() === Response::HTTP_OK
            && !empty($body['message'])
            && $body['message'] === self::SENT
        );
    }
}
