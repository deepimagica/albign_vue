<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function getLoginPage()
    {
        return Inertia::render('Admin/LoginForm');
    }
}
