<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny-dashboard');

        return view('admin.dashboard');
    }
}
