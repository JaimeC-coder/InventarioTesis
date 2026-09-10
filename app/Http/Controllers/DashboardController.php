<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function ecommerce(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('admin.ecommerce');
    }

    public function dashboard1(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('admin.dashboard');
    }

    public function chatbot(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('admin.chatbot');
    }

    public function reports(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        return view('admin.reports');
    }
}
