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
    protected FetchXmlReport $fetchXmlService;
    protected ItemImportService $itemImportService;
    protected ItemRepository $itemRepository;
    protected AlmaItemService $almaItemService;

    /**
     * Construct the ItemOperationsController.
     *
     * @param  FetchXmlReport    $fetchXmlService    Service for fetching XML reports.
     * @param  ItemImportService $itemImportService  Service for importing items.
     * @param  ItemRepository    $itemRepository     Repository for managing items.
     * @param  AlmaItemService   $almaItemService        Service for interacting with Alma API.
     * @return void
     */
    public function __construct(
        FetchXmlReport $fetchXmlService,
        ItemImportService $itemImportService,
        ItemRepository $itemRepository,
        AlmaItemService $almaItemService
    ) {
        $this->fetchXmlService = $fetchXmlService;
        $this->itemImportService = $itemImportService;
        $this->itemRepository = $itemRepository;
        $this->almaItemService = $almaItemService;
    }

    /**
     * Display the view for retrieving new items.
     *
     * @return View
     */
    public function retrieveNewItemsView()
    {

        // Retrieve the selected item count from the form submission or default to 25
        $selectedItemCount = request()->input('itemCount', 25);

       // return view('operations.retrieve_new_items', compact('selectedItemCount'));

        $actionRoute = route('retrieve-new-items');
        return view('operations.retrieve_new_items', compact('selectedItemCount', 'actionRoute'));

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

            // return the view with the variables
            $actionRoute = route('retrieve-new-items');
            return view('operations.retrieve_new_items', [
                'items' => $updatedItems,
                'selectedItemCount' => $itemCount,
                'actionRoute' => $actionRoute
            ]);


        } catch (Exception $e) {
            Log::error('Error fetching or processing data: ' . $e->getMessage());
            // Flash error message
            Flash::error( 'Error fetching or processing data: ' . $e->getMessage());
            // Redirect back to the retrieve new items page
            return redirect()->route('retrieve-new-items');
        }
    }

    /**
     * Display the view for processing database items.
     *
     * @return View
     */
    public function processItemsView()
    {

        // Retrieve the selected item count from the form submission or default to 25
        $selectedItemCount = request()->input('itemCount', 25);
        $actionRoute = route('process-items');

        return view('operations.process_items', compact('selectedItemCount', 'actionRoute'));
    }




    /**
     * Process and update items based on the selected count.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function processItems(Request $request)
    {
        // Retrieve the selected item count from the form submission
        $itemCount = $request->input('itemCount', 10);

        // Store the initial operation details in the operations table
        $operation = Operation::create([
            'operation_name' => 'Process Items',
            'item_count' => 0,
            'operation_type' => 'process'
        ]);

        // Fetch 'new' items from the database up to the defined count
        $items = Item::where('status', 'new')->take($itemCount)->get();

        // Process each item
        foreach ($items as $item) {
            try {
                // Retrieve the Alma item
                $almaItem = $this->almaItemService->getAlmaItem($item->mms_id, $item->holding_id, $item->physical_item_id);

                // Update the Alma item's replacement cost
                $almaItem = $this->almaItemService->updateReplacementCost($item, $almaItem);

                // Attempt to update the Alma item without making any changes
                $updateResult = $this->almaItemService->updateAlmaItem($almaItem);

                // Check if the update was successful
                if ($updateResult && $updateResult['status'] === 200) {
                    // Mark the item as processed and set the last operation ID
                    $item->status = 'processed';
                    $item->last_operation_id = $operation->id;
                    $item->save();
                } else {
                    // Mark the item as failed
                    $item->status = 'failed';
                    $item->last_operation_id = $operation->id;
                    $item->save();
                }
            } catch (Exception $e) {
                // Handle any exceptions and mark the item as failed
                $item->status = 'failed';
                $item->last_operation_id = $operation->id;
                $item->save();
                Log::error('Failed to find/update item: ' . $e->getMessage());
                Flash::error('Failed to find/update item: ' . $e->getMessage());
            }
        }

        // Update the item count in the operations table
        $operation->item_count = Item::where('last_operation_id', $operation->id)->count();
        $operation->save();

        // Fetch items based on their statuses for the current operation
        $processedItems = Item::where('last_operation_id', $operation->id)
            ->where('status', 'processed')
            ->get();

        $failedItems = Item::where('last_operation_id', $operation->id)
            ->where('status', 'failed')
            ->get();

        $retrievedItems = Item::where('last_operation_id', $operation->id)
            ->get();

        // Flash success message
        Flash::success('Successfully processed ' . count($processedItems) . ' Alma items.');

        // Log retrieved, processed, and failed items
//        Log::info('Retrieved Items: ' . $retrievedItems->toJson());
//        Log::info('Processed Items: ' . $processedItems->toJson());
//        Log::info('Failed Items: ' . $failedItems->toJson());

        // Define the action route for the view
        $actionRoute = route('process-items');

        // Redirect back to the process items page with the updated items
        return view('operations.process_items', [
            'retrievedItems' => $retrievedItems,
            'failedItems' => $failedItems,
            'processedItems' => $processedItems,
            'selectedItemCount' => $itemCount,
            'actionRoute' => $actionRoute
        ]);
    }


}
