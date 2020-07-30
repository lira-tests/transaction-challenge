<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class PayerHasAmount implements Rule
{
    protected float $amount;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public function passes($attribute, $value)
    {
        $user = User::with('wallets')->find($value);
        return (
            $user->wallets->amount > 0
            && $user->wallets->amount > $this->amount
            && $this->amount > 0
        );
    }

    public function message()
    {
        return 'The :attribute need amount to create this transaction.';
    }
}
