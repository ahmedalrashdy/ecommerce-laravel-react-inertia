<?php

namespace App\Http\Controllers\Store\Support;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class SupportController extends Controller
{
    public function about()
    {
        return Inertia::render('store/support/about');
    }

    public function contact()
    {
        return Inertia::render('store/support/contact');
    }

    public function help()
    {
        return Inertia::render('store/support/help');
    }


    public function returns()
    {
        return Inertia::render('store/support/returns');
    }
}
