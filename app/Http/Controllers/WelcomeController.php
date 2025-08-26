<?php

namespace App\Http\Controllers;

use App\Http\Middleware\WithLandingPageMiddleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller as BaseController;

class WelcomeController extends BaseController
{
    public function __construct()
    {
        $this->middleware(WithLandingPageMiddleware::class);
    }

    public function index()
    {
        return Inertia::render('welcome/index');
    }
}
