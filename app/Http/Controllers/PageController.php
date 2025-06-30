<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(Page $page): View|RedirectResponse
    {
        return view('pages.show', ['page' => $page]);
    }
}
