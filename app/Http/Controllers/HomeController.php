<?php

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\Models\Page;

class HomeController extends Controller
{
    public function index()
    {
        $homepageId = Settings::get('general.homepage');
        if ($homepageId && $page = Page::find($homepageId)) {
            return app(PageController::class)->show($page);
        }

        return view('welcome');
    }
}
