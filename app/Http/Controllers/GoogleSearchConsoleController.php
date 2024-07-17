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
        $accessToken = json_decode($user->google_token, true);
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired()) {
            if (isset($accessToken['refresh_token'])) {
                $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);
                $newToken = $client->getAccessToken();
                $newToken['refresh_token'] = $accessToken['refresh_token']; // Retain the refresh token
                $user->google_token = json_encode($newToken);
                $user->save();
            } else {
                return response()->json(['success' => false, 'message' => 'Refresh token is missing'], 403);
            }
        }

        $service = new Google_Service_Webmasters($client);
        $sites = $service->sites->listSites();

        // Fetch current sites from the database
        $existingSites = $user->sites()->pluck('site_url')->toArray();
        $fetchedSites = array_map(function ($site) {
            return [
                'siteUrl' => $site->siteUrl,
                'permissions' => $this->mapPermissions($site->permissionLevel), // Use the mapping method here
            ];
        }, $sites->getSiteEntry());

        // Determine which sites to add and which to remove
        $sitesToAdd = array_filter($fetchedSites, function ($fetchedSite) use ($existingSites) {
            return !in_array($fetchedSite['siteUrl'], $existingSites);
        });

        $sitesToRemove = array_diff($existingSites, array_column($fetchedSites, 'siteUrl'));

        // Remove outdated sites
        if (!empty($sitesToRemove)) {
            $user->sites()->whereIn('site_url', $sitesToRemove)->delete();
        }

        // Add new sites and update permissions for existing sites
        foreach ($fetchedSites as $siteData) {
            $user->sites()->updateOrCreate(
                ['site_url' => $siteData['siteUrl']],
                ['permissions' => $siteData['permissions']]
            );
        }

        // Update sitemaps for each site
        foreach ($user->sites as $site) {
            $sitemaps = $service->sitemaps->listSitemaps($site->site_url);
            $existingSitemaps = $site->sitemaps()->pluck('url')->toArray();
            $fetchedSitemaps = array_map(function ($sitemap) {
                return $sitemap->path;
            }, $sitemaps->getSitemap());

            $sitemapsToAdd = array_diff($fetchedSitemaps, $existingSitemaps);
            $sitemapsToRemove = array_diff($existingSitemaps, $fetchedSitemaps);

            if (!empty($sitemapsToRemove)) {
                $site->sitemaps()->whereIn('url', $sitemapsToRemove)->delete();
            }

            foreach ($sitemapsToAdd as $sitemapUrl) {
                $site->sitemaps()->create([
                    'url' => $sitemapUrl,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }


    private function mapPermissions($permission)
    {
        $permissionsMap = [
            'siteOwner' => 'Owner',
            'siteFullUser' => 'Full',
            'siteRestrictedUser' => 'Restricted',
            'siteUnverifiedUser' => 'Unverified',
        ];

        return $permissionsMap[$permission] ?? $permission;
    }
}
