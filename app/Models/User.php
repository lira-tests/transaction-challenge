<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes, Uuid;

    const TYPE_PERSON = 'person';

    const TYPE_COMPANY = 'company';

    protected $fillable = [
        'name', 'email',
    ];

    protected $hidden = [
        'password',
    ];

    public $incrementing = false;

    public function wallets()
    {
        return $this->hasOne(Wallet::class);
    }
}
