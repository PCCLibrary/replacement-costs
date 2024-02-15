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
use Laracasts\Flash\Flash;

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
//        $retrievedItems = $this->itemRepository->all();

        //return view('operations.retrieve_new_items'); //->with('items', $retrievedItems);

        // Retrieve the selected item count from the form submission or default to 25
        $selectedItemCount = request()->input('itemCount', 25);

        return view('operations.retrieve_new_items', compact('selectedItemCount'));
    }

    /**
     * Retrieve and process new items from the Analytics XML Report.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View|\Illuminate\Http\RedirectResponse
     */
    public function retrieveNewItems(Request $request)
    {
        // Retrieve the selected item count from the form submission
        $itemCount = $request->input('itemCount');

        try {
            // Fetch XML data with the selected item count
            $xmlData = $this->fetchXmlService->fetchData($itemCount);
            Log::info('API request successful. XML data retrieved: ' . strlen($xmlData) . ' bytes');

            // Parse XML data
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

            // Retrieve items updated during the current operation
            $updatedItems = Item::where('last_operation_id', $operation->id)->get();

//            // Flash success message
//            Flash::success( count($retrievedItems) . ' items retrieved and stored successfully.');

            // Redirect back to the retrieve new items page
            return view('operations.retrieve_new_items', [
                'items' => $updatedItems,
                'selectedItemCount' => $itemCount
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching or processing data: ' . $e->getMessage());
            // Flash error message
            Flash::error( 'Error fetching or processing data: ' . $e->getMessage());
            // Redirect back to the retrieve new items page
            return redirect()->route('retrieve-new-items');
        }
    }

}
