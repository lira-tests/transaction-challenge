<?php

namespace App\Services\Authorizers;

class DefaultAuthorizer implements AuthorizerInterface
{
    public function approved(): bool
    {
        return true;
    }
}
