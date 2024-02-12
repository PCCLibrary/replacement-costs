<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

/**
 * Service class responsible for fetching an XML report from an Alma Analytics API endpoint.
 * It uses Laravel's HTTP facade to make the request and
 * handles any unsuccessful responses by flashing an error message to the session.
 *
 * @package App\Services
 * @version 1.0
 */

class FetchXmlReport
{
    private $apiUrl = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1/analytics/reports';
    private $apiKey = 'l7xxd7d119c8cd934725af925d147c209cdf';
    private $limit = 25;

    /**
     * Fetch the XML report data.
     *
     * @return string|null The XML response string or null if the request failed.
     */
    public function fetchData()
    {
        // Make the API request using Guzzle HTTP Client (via Laravel's Http facade)
        $response = Http::withHeaders(['Accept' => 'application/xml'])
            ->get($this->apiUrl, [

                'path' => '/shared/Portland Community College/Reports/Tech Services Reports/mdw reports/mdw-items without Replacement cost',
                'apikey' => $this->apiKey,
                'limit' => $this->limit,
            ]);

        // Check if the API response is successful
        if (!$response->successful()) {
            Session::flash('error', 'Error fetching data from API: ' . $response->body());
            return null;
        }

        // Return the XML data from the API response
        return $response->body();
    }
}
