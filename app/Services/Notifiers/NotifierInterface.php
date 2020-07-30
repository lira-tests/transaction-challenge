<?php

namespace App\Services\Notifiers;

interface NotifierInterface
{
    public function send(): bool;
}
