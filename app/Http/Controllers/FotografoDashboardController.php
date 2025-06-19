<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class FotografoDashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Fotografo/Dashboard');
    }
}
