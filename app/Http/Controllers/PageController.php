<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageController extends Controller
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
     * Display the specified page.
     */
    public function show(Page $page): View|RedirectResponse
    {
        $this->authorize('view', $page);

        // Use the service to get the page with all necessary relationships
        $page = $this->pageService->getPageWithContent($page);

        return view('pages.show', ['page' => $page]);
    }
}
