<?php

namespace App\Http\Controllers;

use App\Models\Site;

use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index(Site $site)
    {
        // You can load the sitemaps for the given site and pass them to the view
        $sitemaps = $site->sitemaps;

        return view('pages.sitemaps', compact('site', 'sitemaps'));
    }
}
