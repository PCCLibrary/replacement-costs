<?php

namespace App\Http\Controllers;

use App\Services\FetchXmlReport;
use App\Services\ItemImportService;
use App\Repositories\ItemRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ItemOperationsController extends Controller
{
    protected $fetchXmlService;
    protected $itemImportService;
    protected $itemRepository;

    public function __construct(
        FetchXmlReport $fetchXmlService,
        ItemImportService $itemImportService,
        ItemRepository $itemRepository
    ) {
        $this->fetchXmlService = $fetchXmlService;
        $this->itemImportService = $itemImportService;
        $this->itemRepository = $itemRepository;
    }

    public function retrieveNewItems()
    {
        try {
            // Fetch the XML data using the fetchXmlService
            $xmlData = $this->fetchXmlService->fetchData();

            // Log the successful API request
            Log::info('API request successful. XML data retrieved: ' . strlen($xmlData) . ' bytes');

            // Parse the XML data using the itemImportService
            $retrievedItems = $this->itemImportService->processXmlData($xmlData);

            // Log the successful parsing of XML data
            Log::info('XML data parsed successfully. Items retrieved: ' . count($retrievedItems));

            // Store items in the database using the itemRepository
            $this->itemRepository->addOrUpdateItems($retrievedItems);

            // Flash the message for the user
            session()->flash('success', count($retrievedItems) . ' items retrieved and stored successfully.');

            // Redirect back to the retrieve items page with a success message
            return redirect()->route('operations.retrieve_items');
        } catch (Exception $e) {
            // Log the error
            Log::error('Error fetching or processing data: ' . $e->getMessage());

            // Flash an error message for the user
            session()->flash('error', 'Error fetching or processing data: ' . $e->getMessage());

            // Redirect back to the retrieve items page with an error message
            return redirect()->route('operations.retrieve_items');
        }
    }
}
