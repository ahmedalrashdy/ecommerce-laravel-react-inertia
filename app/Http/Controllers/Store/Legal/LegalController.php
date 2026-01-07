<?php

namespace App\Http\Controllers\Store\Legal;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class LegalController extends Controller
{

    public function privacy()
    {
        return Inertia::render('store/legal/privacy');
    }

    public function terms()
    {
        return Inertia::render('store/legal/terms');
    }

}
