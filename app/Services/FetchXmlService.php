<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
    private $reportPath = '/shared/Portland Community College/Reports/Tech Services Reports/mdw reports/mdw-items without Replacement cost';
    private $apiKey = 'l7xxd7d119c8cd934725af925d147c209cdf';
    private $limit = 25;

    /**
     * Fetch the XML report data.
     *
     * @param string|null $operationType The type of operation ("new" or "continue").
     * @param int|null $itemCount The number of items to retrieve.
     * @param string|null $resumptionToken The resumption token for continue operations.
     * @return string|null The XML response string or null if the request failed.
     */
    public function fetchData($operationType = 'new', $itemCount = null, $resumptionToken = null)
    {
        // Initialize the request URL string
        $requestUrl = $this->apiUrl . '?';

        // Log the operation type and item count for debugging
//        Log::info('Operation Type in fetchData: ' . $operationType);
//        Log::info('Item Count in fetchData: ' . $itemCount);
//        Log::info('Default Limit in fetchData: ' . $this->limit);

        // Assign the appropriate item count based on the operation type
        $item_count = $itemCount ?? $this->limit;

        // Log the final item count after assignment
//        Log::info('Final Item Count in fetchData: ' . $item_count);

        // Add the appropriate parameters based on the operation type
        if ($operationType === 'new') {
            // For new operations, include the report path, apikey, limit, and col_names
            $requestUrl .= 'path=' . $this->reportPath . '&';
        } elseif ($operationType === 'continue') {
            // For continue operations, include the resumption token, apikey, limit, and col_names
            $requestUrl .= 'token=' . $resumptionToken . '&';
        }

        $requestUrl .= 'apikey=' . $this->apiKey . '&';
        $requestUrl .= 'limit=' . $item_count . '&';
        $requestUrl .= 'col_names=true';

        // Log the request URL
        Log::info('API Request URL: ' . $requestUrl);

        // Make the API request using Guzzle HTTP Client (via Laravel's Http facade)
        $response = Http::withHeaders(['Accept' => 'application/xml'])
            ->get($requestUrl);

        // Check if the API response is successful
        if (!$response->successful()) {
            Flash::error('Error fetching data from API: ' . $response->body());
            return null;
        }

        // Report success retrieving items
        Flash::success( $item_count. ' Items retrieved from the Analytics report.');

        // Log the XML data
        $xmlData = $response->body();
//        Log::info('XML Data: ' . $xmlData);

        // Return the XML data from the API response
        return $xmlData;
    }

}
