<?php

namespace App\Services\Notifiers;

use App\Services\Notifiers\NotifierInterface;

class DefaultNotifier implements NotifierInterface
{
    public function send(): bool
    {
        return true;
    }
}
