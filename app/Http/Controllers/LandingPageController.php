<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('landing', compact('plans'));
    }
}
