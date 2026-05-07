<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpaController extends Controller
{
    public function index()
    {
        return view('app');
    }

    public function terms()
    {
        return view('terms-popup');
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function security()
    {
        return view('security');
    }
}
