<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Constructor.
     */
    public function __construct(
        /**
         * Page service instance.
         */
        protected PageService $pageService
    )
    {
    }

    /**
     * Display the homepage.
     */
    public function index(): View
    {
        $homepageId = Settings::get('general.homepage');
        
        if ($homepageId && $page = Page::find($homepageId)) {
            $this->authorize('view', $page);

            // Use the service to get the page with all necessary relationships
            $page = $this->pageService->getPageWithContent($page);

            return app(PageController::class)->show($page);
        }

        return view('welcome');
    }
}
