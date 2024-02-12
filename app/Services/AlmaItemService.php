<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use stdClass;


/**
 *
 */
class AlmaItemService
{
    /**
     * @var string
     */
    protected string $baseApiUrl = 'https://api-na.hosted.exlibrisgroup.com/almaws/v1';
    /**
     * @var string
     */
    protected string $apiKey = 'l7xxd7d119c8cd934725af925d147c209cdf'; // Consider placing this in an environment variable or config.

    /**
     * Retrieves an Alma item by its MMS ID, holding ID, and item PID.
     *
     * @param string $mmsId    The MMS ID of the Alma item.
     * @param string $holdingId The holding ID of the Alma item.
     * @param string $itemPid   The item PID of the Alma item.
     *
     * @return array           The Alma item data in array format.
     *
     * @throws Exception       Throws an exception if the API request fails.
     */
    public function getAlmaItem(string $mmsId, string $holdingId, string $itemPid): array
    {
        $format = 'application/json';
        $url = "{$this->baseApiUrl}/bibs/{$mmsId}/holdings/{$holdingId}/items/{$itemPid}?apikey={$this->apiKey}&format=json&view=brief";

        Log::debug('getAlmaItem requested URL: ' . $url);
        try {
            $response = Http::withHeaders([
                'Accept' => $format,
            ])->get($url);

            // Log the response data before and after decoding
            Log::debug('Alma item data before decoding: ' . $response->body());
            $itemData = json_decode($response->body(), true);
            Log::debug('Alma item data after decoding: ' . json_encode($itemData));

            if ($response->successful()) {
                return $itemData;
            } else {
                Log::error('Error response from API for MMS ID ' . $mmsId . ': ' . $response->body());
                throw new Exception("Failed to retrieve the item in JSON. Response: " . $response->body());
            }
        } catch (Exception $e) {
            Log::error('Error fetching Alma item with MMS ID ' . $mmsId . ': ' . $e->getMessage());
            throw $e;
        }
    }


        /**
         * Updates an Alma item with the provided item data.
         *
         * @param array $almaItemData The Alma item data retrieved from the API.
         *
         * @return array|false The updated Alma item as an object, or false if the update fails.
         * @throws Exception Throws an exception if the API request fails.
         */
        public function updateAlmaItem(array $almaItemData)
        {
            $url = $almaItemData['link'] . '?apikey=' . $this->apiKey . '&format=json';

            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            // Create a copy of the Alma item data
            $requestData = $almaItemData;

            // Call the recursive method to convert empty arrays to empty objects
            $requestData = $this->convertEmptyArraysToObjects($requestData);

            try {
                $response = Http::withHeaders($headers)->put($url, $requestData);

                Log::info('updateAlmaItem requesting URL: ' . $url);
                Log::info('updateAlmaItem request data: ' . json_encode($requestData)); // Log the request data

                if ($response->successful()) {
                    Log::info('Updated Alma item: ' . $response->status());
                    return [ // Return an array containing the Alma item and status
                        'item' => json_decode($response->body()),
                        'status' => $response->status()
                    ];
                } else {
                    Log::error('Error updating Alma item: ' . $response->status() . ' - ' . $response->body());
                    return false;
                }
            } catch (Exception $e) {
                Log::error('Error updating Alma item: ' . $e->getMessage());
                throw $e;
            }
        }


    /**
     * Recursively converts empty arrays to empty objects in the given data array.
     *
     * @param array $data The data array to process.
     *
     * @return array The data array with empty arrays converted to empty objects.
     */
    private function convertEmptyArraysToObjects(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value) && empty($value)) {
                $data[$key] = (object) [];
            } elseif (is_array($value)) {
                $data[$key] = $this->convertEmptyArraysToObjects($value);
            }
        }

        return $data;
    }


    /**
     * Updates the Alma item's replacement cost using the po_line_reference from a database item.
     *
     * @param object $dbItem The database item, expected to have the po_line_reference field.
     * @param array $almaItem
     * @return array             The updated Alma item data array.
     */
    public function updateReplacementCost(object $dbItem, array $almaItem): array
    {
        $replacement_cost = number_format((float)$dbItem['replacement_cost'], 2, '.', '');
        $almaItem['item_data']['replacement_cost'] = $replacement_cost;
        return $almaItem;
    }

}
