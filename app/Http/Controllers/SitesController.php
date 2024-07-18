<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Helpers\GoogleSearchConsoleHelper;
use Illuminate\View\View;

class SitesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.sites');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $site = Site::findOrFail($id);

        $totalIndexedPages = GoogleSearchConsoleHelper::getIndexedPages($site->site_url);
        $inProgress = GoogleSearchConsoleHelper::getInProgressPages($site->site_url);
        $notIndexed = GoogleSearchConsoleHelper::getNotIndexedPages($site->site_url);
        $notChecked = GoogleSearchConsoleHelper::getNotCheckedPages($site->site_url);

        return view('sites.show', compact('site', 'totalIndexedPages', 'inProgress', 'notIndexed', 'notChecked'));
    }




    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
