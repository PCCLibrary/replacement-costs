<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laracasts\Flash\Flash;

/**
 * Service class responsible for fetching an XML report from an Alma Analytics API endpoint.
 * It uses Laravel's HTTP facade to make the request and
 * handles any unsuccessful responses by flashing an error message to the session.
 *
 * @package App\Services
 * @version 1.0
 */

class FetchXmlService
{
    private $apiUrl = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1/analytics/reports';
    private $apiKey = 'l7xxd7d119c8cd934725af925d147c209cdf';
    private $limit = 25;

    /**
     * Fetch the XML report data.
     *
     * @return string|null The XML response string or null if the request failed.
     */
    public function fetchData($itemCount = null, $resumptionToken = null)
    {

        // Use the provided item count as the limit if it's set, otherwise use the default limit
        $limit = $itemCount ?? $this->limit;

        // Make the API request using Guzzle HTTP Client (via Laravel's Http facade)
        $response = Http::withHeaders(['Accept' => 'application/xml'])
            ->get($this->apiUrl, [

                'path' => '/shared/Portland Community College/Reports/Tech Services Reports/mdw reports/mdw-items without Replacement cost',
                'apikey' => $this->apiKey,
                'limit' => $limit,
            ]);

        // Check if the API response is successful
        if (!$response->successful()) {
            Flash::error('Error fetching data from API: ' . $response->body());
            return null;
        }

        // report success retrieving items
        Flash::success($itemCount . ' items retrieved from the Analytics report.');

        // Return the XML data from the API response
        return $response->body();
    }
}
