<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Webmasters;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
                $numberOfUrls = array_reduce($sitemap->contents, function ($carry, $content) {
                    return $carry + (isset($content->submitted) ? (int) $content->submitted : 0);
                }, 0);

                return [
                    'url' => $sitemap->path,
                    'number_of_urls' => $numberOfUrls,
                    'is_index' => $sitemap->isSitemapsIndex ?? false,
                    'enabled' => true, // Assuming all new sitemaps are enabled by default
                    'errors' => (int) $sitemap->errors,
                    'is_pending' => (bool) $sitemap->isPending,
                    'last_downloaded' => $sitemap->lastDownloaded ? \Carbon\Carbon::parse($sitemap->lastDownloaded) : null,
                    'last_submitted' => $sitemap->lastSubmitted ? \Carbon\Carbon::parse($sitemap->lastSubmitted) : null,
                    'warnings' => (int) $sitemap->warnings,
                ];
            }, $sitemaps->getSitemap());

            $sitemapsToAdd = array_filter($fetchedSitemaps, function ($fetchedSitemap) use ($existingSitemaps) {
                return !in_array($fetchedSitemap['url'], $existingSitemaps);
            });

            $sitemapsToRemove = array_diff($existingSitemaps, array_column($fetchedSitemaps, 'url'));

            if (!empty($sitemapsToRemove)) {
                $site->sitemaps()->whereIn('url', $sitemapsToRemove)->delete();
            }

            foreach ($sitemapsToAdd as $sitemapData) {
                $site->sitemaps()->updateOrCreate(
                    ['url' => $sitemapData['url']],
                    [
                        'number_of_urls' => $sitemapData['number_of_urls'],
                        'is_index' => $sitemapData['is_index'],
                        'enabled' => $sitemapData['enabled'],
                        'errors' => $sitemapData['errors'],
                        'is_pending' => $sitemapData['is_pending'],
                        'last_downloaded' => $sitemapData['last_downloaded'],
                        'last_submitted' => $sitemapData['last_submitted'],
                        'warnings' => $sitemapData['warnings'],
                    ]
                );
            }
        }

        return response()->json(['success' => true]);
    }

    private function mapPermissions($permissionLevel)
    {
        $map = [
            'siteOwner' => 'Owner',
            'siteFullUser' => 'Full',
            'siteRestrictedUser' => 'Restricted',
            'siteUnverifiedUser' => 'Unverified',
        ];

        return $map[$permissionLevel] ?? 'Unknown';
    }


    public function refreshSitemaps($siteId)
    {
        $user = Auth::user();
        if (!$user->google_token) {
            //    Log::error('User not connected to Google Search Console', ['user_id' => $user->id]);
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
                //   Log::error('Refresh token is missing', ['user_id' => $user->id]);
                return response()->json(['success' => false, 'message' => 'Refresh token is missing'], 403);
            }
        }

        $service = new Google_Service_Webmasters($client);
        $site = $user->sites()->findOrFail($siteId);

        try {
            $sitemaps = $service->sitemaps->listSitemaps($site->site_url);
        } catch (Exception $e) {
            //   Log::error('Failed to fetch sitemaps from Google Search Console', ['site_id' => $siteId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch sitemaps from Google Search Console'], 500);
        }

        $existingSitemaps = $site->sitemaps()->pluck('url')->toArray();
        $fetchedSitemaps = array_map(function ($sitemap) {
            return [
                'url' => $sitemap->path,
                'number_of_urls' => collect($sitemap->contents)->sum('submitted'),
                'is_index' => $sitemap->isSitemapsIndex,
                'errors' => $sitemap->errors,
                'is_pending' => $sitemap->isPending,
                'last_downloaded' => Carbon::parse($sitemap->lastDownloaded)->format('Y-m-d H:i:s'),
                'last_submitted' => Carbon::parse($sitemap->lastSubmitted)->format('Y-m-d H:i:s'),
                'warnings' => $sitemap->warnings,
                'enabled' => true,
            ];
        }, $sitemaps->getSitemap());

        // Log::info('Fetched sitemaps from Google Search Console', ['site_id' => $siteId, 'sitemaps' => $fetchedSitemaps]);

        $sitemapsToAdd = array_filter($fetchedSitemaps, function ($fetchedSitemap) use ($existingSitemaps) {
            return !in_array($fetchedSitemap['url'], $existingSitemaps);
        });

        $sitemapsToRemove = array_diff($existingSitemaps, array_column($fetchedSitemaps, 'url'));

        // Log::info('Sitemaps to add', ['site_id' => $siteId, 'sitemaps_to_add' => $sitemapsToAdd]);
        // Log::info('Sitemaps to remove', ['site_id' => $siteId, 'sitemaps_to_remove' => $sitemapsToRemove]);

        if (!empty($sitemapsToRemove)) {
            $site->sitemaps()->whereIn('url', $sitemapsToRemove)->delete();
            //   Log::info('Removed sitemaps', ['site_id' => $siteId, 'sitemaps_to_remove' => $sitemapsToRemove]);
        }

        foreach ($sitemapsToAdd as $sitemapData) {
            $site->sitemaps()->create($sitemapData);
            //  Log::info('Added sitemap', ['site_id' => $siteId, 'sitemap' => $sitemapData]);
        }

        // Update existing sitemaps
        foreach ($fetchedSitemaps as $fetchedSitemap) {
            $site->sitemaps()->updateOrCreate(
                ['url' => $fetchedSitemap['url']],
                $fetchedSitemap
            );
            //  Log::info('Updated sitemap', ['site_id' => $siteId, 'sitemap' => $fetchedSitemap]);
        }

        return response()->json(['success' => true]);
    }
}
