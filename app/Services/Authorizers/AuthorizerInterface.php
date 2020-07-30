<?php

namespace App\Services\Authorizers;

interface AuthorizerInterface
{
    public function approved(): bool;
}
