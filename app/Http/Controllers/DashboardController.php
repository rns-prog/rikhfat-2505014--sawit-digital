<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('dashboard', [
            'kebuns' => $request->user()->kebuns()->latest()->get(),
            'dataContoh' => DataContoh::dashboard(),
        ]);
    }
}
