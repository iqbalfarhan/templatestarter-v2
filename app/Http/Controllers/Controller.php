<?php

namespace App\Http\Controllers;

use App\Models\User;

abstract class Controller
{
    protected ?User $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }
}
