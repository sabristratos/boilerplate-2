<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(Page $page): View
    {
        return view('pages.show', compact('page'));
    }
}
