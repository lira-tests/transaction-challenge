<?php

namespace App\Services;

use App\Exceptions\AuthorizationFailException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class AuthorizationService
{
    const SERVICE_URL = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6/%s';

    const AUTHORIZED = 'Autorizado';

    public function approved($transactionId): bool
    {
        $response = $this->process($transactionId);

        if (!$response) {
            throw new AuthorizationFailException();
        }

        return true;
    }

    private function process($transactionId) : bool
    {
        $response = $this->request($transactionId);

        $body = $response->json();

        return $this->isApproved($response, $body);
    }

    private function request($transactionId)
    {
        return Http::get(sprintf(self::SERVICE_URL, $transactionId));
    }

    private function isApproved($response, $body) : bool
    {
        return (
            $response->status() === Response::HTTP_OK
            && !empty($body['message'])
            && $body['message'] === self::AUTHORIZED
        );
    }
}
