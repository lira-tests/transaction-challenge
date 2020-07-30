<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use Uuid;

    const STATUS_CREATED = 'created';

    const STATUS_EXECUTED = 'executed';

    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'payer_user_id', 'payee_user_id', 'amount', 'reason', 'status'
    ];

    public $incrementing = false;

    public function payer()
    {
        return $this->hasOne(User::class, 'id', 'payer_user_id');
    }

    public function payee()
    {
        return $this->hasOne(User::class, 'id', 'payee_user_id');
    }
}
