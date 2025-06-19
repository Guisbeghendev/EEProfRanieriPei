<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Admin/Dashboard');
    }
}
