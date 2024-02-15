<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Operation;
use App\Services\AlmaItemService;
use App\Services\FetchXmlReport;
use App\Services\ItemImportService;
use App\Repositories\ItemRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ItemOperationsController extends Controller
{
    protected $fetchXmlService;
    protected $itemImportService;
    protected $itemRepository;
    protected $almaService;

    /**
     * Construct the ItemOperationsController.
     *
     * @param  FetchXmlReport    $fetchXmlService    Service for fetching XML reports.
     * @param  ItemImportService $itemImportService  Service for importing items.
     * @param  ItemRepository    $itemRepository     Repository for managing items.
     * @param  AlmaItemService   $almaService        Service for interacting with Alma API.
     * @return void
     */
    public function __construct(
        FetchXmlReport $fetchXmlService,
        ItemImportService $itemImportService,
        ItemRepository $itemRepository,
        AlmaItemService $almaService
    ) {
        $this->fetchXmlService = $fetchXmlService;
        $this->itemImportService = $itemImportService;
        $this->itemRepository = $itemRepository;
        $this->almaService = $almaService;
    }

    /**
     * Display the view for retrieving new items.
     *
     * @return View
     */
    public function retrieveNewItemsView()
    {
        $retrievedItems = $this->itemRepository->all();

        return view('operations.retrieve_new_items')->with('items', $retrievedItems);
    }

    /**
     * Retrieve and process new items from the Alma API.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function retrieveNewItems()
    {
        try {
            $xmlData = $this->fetchXmlService->fetchData();
            Log::info('API request successful. XML data retrieved: ' . strlen($xmlData) . ' bytes');
            $retrievedItems = $this->itemImportService->processXmlData($xmlData);
            Log::info('XML data parsed successfully. Items retrieved: ' . count($retrievedItems));

            // Store the operation details in the operations table
            $operation = Operation::create([
                'operation_name' => 'Retrieve New Items',
                'item_count' => count($retrievedItems),
                'operation_type' => 'retrieve',
            ]);

            // Pass operation ID to the item repository for storing items
            $this->itemRepository->addOrUpdateItems($retrievedItems, $operation->id);

            session()->flash('success', count($retrievedItems) . ' items retrieved and stored successfully.');
            return redirect()->route('retrieve-new-items');
        } catch (Exception $e) {
            Log::error('Error fetching or processing data: ' . $e->getMessage());
            session()->flash('error', 'Error fetching or processing data: ' . $e->getMessage());
            return redirect()->route('retrieve-new-items');
        }
    }
}
