<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Halaman Home (Dashboard simple)
     */
    public function index(): Response
    {
        // auth user sudah otomatis dikirim ke Inertia dari middleware,
        // jadi di Vue kamu cukup pakai usePage().props.auth.user
        return Inertia::render('Home/dashboard');
    }
}
