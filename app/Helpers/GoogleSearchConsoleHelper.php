<?php

namespace App\Helpers;

use Google_Client;
use Google_Service_Webmasters;
use Google_Service_Webmasters_SearchAnalyticsQueryRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class GoogleSearchConsoleHelper
{
    public static function initializeClient()
    {
        $user = Auth::user();
        if (!$user->google_token) {
            Log::error('Google token is missing');
            return null;
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
                Log::error('Refresh token is missing');
                return null;
            }
        }

        return new Google_Service_Webmasters($client);
    }


    /**
     * Get the number of indexed pages for a given site URL.
     *
     * @param string $siteUrl The URL of the site to check.
     * @return int The number of indexed pages.
     */
    public static function getIndexedPages($siteUrl)
    {
        // Initialize the Google Search Console client
        $service = self::initializeClient();
        if (!$service) {
            return 0;
        }

        // Create a new search analytics query request
        $query = new Google_Service_Webmasters_SearchAnalyticsQueryRequest();

        // Set the dimensions to 'page' to get data for each page
        $query->setDimensions(['page']);

        // Set the search type to 'web'
        $query->setSearchType('web');

        // Set the date range for the query
        $query->setStartDate('2000-01-01');
        $query->setEndDate(date('Y-m-d'));

        // Set the row limit to 1000
        $query->setRowLimit(1000);

        // Create a dimension filter group to filter pages that contain '/'
        $dimensionFilterGroup = [
            'filters' => [
                [
                    'dimension' => 'page',
                    'operator' => 'contains',
                    'expression' => '/',
                ],
            ],
        ];

        // Add the dimension filter group to the query
        $query->setDimensionFilterGroups([$dimensionFilterGroup]);

        // Execute the query
        $response = $service->searchanalytics->query($siteUrl, $query);

        // Initialize the counter for indexed pages
        $totalIndexedPages = 0;

        // Iterate through the response rows
        foreach ($response->getRows() as $row) {
            // Sum up the clicks as an indication of indexed pages
            $totalIndexedPages += $row->getClicks();
        }

        // Return the count of indexed pages
        return $totalIndexedPages;
    }


    public static function getInProgressPages($siteUrl)
    {
        $service = self::initializeClient();
        if (!$service) {
            return 0;
        }

        $sitemaps = $service->sitemaps->listSitemaps($siteUrl);
        Log::info('Sitemaps:', (array)$sitemaps);

        $inProgressPages = 0;
        foreach ($sitemaps->getSitemap() as $sitemap) {
            foreach ($sitemap->getContents() as $content) {
                $totalUrls = $content->submitted;
                $indexedUrls = $content->indexed;
                Log::info('Sitemap Content:', ['totalUrls' => $totalUrls, 'indexedUrls' => $indexedUrls]);
                $inProgressPages += $totalUrls - $indexedUrls;
            }
        }

        Log::info('In Progress Pages:', ['count' => $inProgressPages]);
        return $inProgressPages;
    }




    /**
     * Get the number of not indexed pages for a given site URL.
     *
     * @param string $siteUrl The URL of the site to check.
     * @return int The number of not indexed pages.
     */
    public static function getNotIndexedPages($siteUrl)
    {
        // Initialize the Google Search Console client
        $service = self::initializeClient();
        if (!$service) {
            return 0;
        }

        // Create a new search analytics query request
        $query = new \Google_Service_Webmasters_SearchAnalyticsQueryRequest();

        // Set the date range for the query
        $query->setStartDate('2000-01-01');
        $query->setEndDate(date('Y-m-d'));

        // Set the dimensions to 'page' to get data for each page
        $query->setDimensions(['page']);

        // Set the row limit to 1000
        $query->setRowLimit(1000);

        // Create a dimension filter group to filter pages that contain '/'
        $dimensionFilterGroup = new \Google_Service_Webmasters_ApiDimensionFilterGroup();
        $dimensionFilter = new \Google_Service_Webmasters_ApiDimensionFilter();
        $dimensionFilter->setDimension('page');
        $dimensionFilter->setOperator('contains');
        $dimensionFilter->setExpression('/');
        $dimensionFilterGroup->setFilters([$dimensionFilter]);

        // Add the dimension filter group to the query
        $query->setDimensionFilterGroups([$dimensionFilterGroup]);

        // Execute the query
        $response = $service->searchanalytics->query($siteUrl, $query);

        // Initialize the counter for not indexed pages
        $notIndexedPages = 0;

        // Iterate through the response rows
        foreach ($response->getRows() as $row) {
            // Count pages that have impressions but no clicks (indicative of not being indexed)
            if ($row->getImpressions() > 0 && $row->getClicks() == 0) {
                $notIndexedPages++;
            }
        }

        // Return the count of not indexed pages
        return $notIndexedPages;
    }


    /**
     * Get the number of pages that have not been checked for indexing status for a given site URL.
     *
     * @param string $siteUrl The URL of the site to check.
     * @return int The number of pages not checked.
     */
    public static function getNotCheckedPages($siteUrl)
    {
        // Initialize the Google Search Console client
        $service = self::initializeClient();
        if (!$service) {
            return 0;
        }

        // Get the list of sitemaps for the site
        $sitemaps = $service->sitemaps->listSitemaps($siteUrl);

        // Initialize the counter for pages not checked
        $notCheckedPages = 0;

        // Iterate through each sitemap
        foreach ($sitemaps->getSitemap() as $sitemap) {
            // Sum up the number of URLs that have not been checked in the sitemap
            foreach ($sitemap->getContents() as $content) {
                $totalUrls = $content->submitted;
                $indexedUrls = $content->indexed;
                $notIndexedUrls = $content->errors + $content->warnings; // Considering errors and warnings as not indexed
                $checkedUrls = $indexedUrls + $notIndexedUrls;
                $notCheckedPages += $totalUrls - $checkedUrls;
            }
        }

        // Return the count of pages not checked
        return $notCheckedPages;
    }
}
