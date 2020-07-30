<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class PayerIsCompany implements Rule
{
    public function passes($attribute, $value)
    {
        $user = User::findOrFail($value);
        return ($user->type === User::TYPE_PERSON);
    }

    public function message()
    {
        return 'The :attribute can\'t be a company.';
    }
}
