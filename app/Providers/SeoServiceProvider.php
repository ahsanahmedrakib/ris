<?php

namespace App\Providers;

use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;

/**
 * Makes sure the website layout always has a metadata object.
 *
 * A controller that knows more about a page passes it an instance; the layout
 * then renders that and stops. A controller that has nothing to say (a static
 * page with no dynamic content) relies on the object built here, so it cannot
 * ship with a missing title, description or canonical URL.
 *
 * The composer is scoped to the layout rather than to every template, because a
 * blade partial renders as its own view and a '*' composer would rebuild the
 * metadata (and re-read the school statistics) once per partial per page.
 */
class SeoServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ViewFacade::composer('layouts.website', function (View $view): void {
            $seo = $view->getData()['seo'] ?? null;

            if ($seo instanceof Seo) {
                return;
            }

            // Admin, parent and PDF pages never render this layout, so everything
            // that reaches this fallback is a public page. It is left indexable
            // even when no page entry exists yet; routes that must never be
            // indexed are handled by the Seo object itself.
            $view->with('seo', Seo::forCurrentPage());
        });
    }
}
