<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        return $this->user->with('wallets')->get();
    }
}
