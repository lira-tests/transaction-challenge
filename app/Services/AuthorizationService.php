<?php

namespace App\Services;

use App\Services\Authorizers\AuthorizerInterface;

class AuthorizationService
{
    protected AuthorizerInterface $authorizer;

    public function __construct(AuthorizerInterface $authorizer)
    {
        $this->authorizer = $authorizer;
    }
}
