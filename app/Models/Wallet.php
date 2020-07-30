<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use Uuid;

    protected $fillable = [
        'amount',
    ];

    protected $casts = [
        'amount' => 'float'
    ];

    public $incrementing = false;

    public function users()
    {
        return $this->belongsTo(users::class);
    }
}
