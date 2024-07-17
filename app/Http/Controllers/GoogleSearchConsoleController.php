<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Webmasters;
use Illuminate\Support\Facades\Auth;

class GoogleSearchConsoleController extends Controller
{
    public function connect()
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(route('google.callback'));
        $client->addScope(Google_Service_Webmasters::WEBMASTERS_READONLY);

        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(route('google.callback'));

        if ($request->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->input('code'));
            $client->setAccessToken($token);

            // Save the token in the user's record
            $user = Auth::user();
            $user->google_token = json_encode($token);
            $user->save();

            return redirect()->route('dashboard.sites');
        }

        return redirect()->route('dashboard.sites')->with('error', 'Failed to connect to Google Search Console');
    }

    public function index()
    {
        $user = Auth::user();
        if (!$user->google_token) {
            return view('pages.sites', ['connected' => false]);
        }

        // Fetch sites from the database only
        $storedSites = $user->sites()->get();

        return view('pages.sites', ['connected' => true, 'sites' => $storedSites]);
    }


    public function refresh()
    {
        $user = Auth::user();
        if (!$user->google_token) {
            return response()->json(['success' => false, 'message' => 'Not connected to Google Search Console'], 403);
        }

        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setAccessToken(json_decode($user->google_token, true));

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $user->google_token = json_encode($client->getAccessToken());
            $user->save();
        }

        $service = new Google_Service_Webmasters($client);
        $sites = $service->sites->listSites();

        // Fetch current sites from the database
        $existingSites = $user->sites()->pluck('site_url')->toArray();
        $fetchedSites = array_map(function ($site) {
            return $site->siteUrl;
        }, $sites->getSiteEntry());

        // Determine which sites to add and which to remove
        $sitesToAdd = array_diff($fetchedSites, $existingSites);
        $sitesToRemove = array_diff($existingSites, $fetchedSites);

        // Remove outdated sites
        if (!empty($sitesToRemove)) {
            $user->sites()->whereIn('site_url', $sitesToRemove)->delete();
        }

        // Add new sites
        foreach ($sitesToAdd as $siteUrl) {
            $user->sites()->create([
                'site_url' => $siteUrl,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
