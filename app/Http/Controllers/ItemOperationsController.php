<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Operation;
use App\Services\AlmaItemService;
use App\Services\FetchXmlService;
use App\Services\ItemImportService;
use App\Repositories\ItemRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Laracasts\Flash\Flash;
use SimpleXMLElement;

class ItemOperationsController extends Controller
{
    protected FetchXmlService $fetchXmlService;
    protected ItemImportService $itemImportService;
    protected ItemRepository $itemRepository;
    protected AlmaItemService $almaItemService;

    // Default selected item count
    private $defaultItemCount = '25';

    /**
     * Construct the ItemOperationsController.
     *
     * @param  FetchXmlService    $fetchXmlService    Service for fetching XML reports.
     * @param  ItemImportService $itemImportService  Service for importing items.
     * @param  ItemRepository    $itemRepository     Repository for managing items.
     * @param  AlmaItemService   $almaItemService        Service for interacting with Alma API.
     * @return void
     */
    public function __construct(
        FetchXmlService   $fetchXmlService,
        ItemImportService $itemImportService,
        ItemRepository    $itemRepository,
        AlmaItemService   $almaItemService
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
        // Get the action route for the form submission
        $actionRoute = route('retrieve-new-items');

        // Set isFinished to true for a new retrieval
        $isFinished = "true";

        // Set the default selected item count
        $selectedItemCount = '25';

        // Set the default resumption token to empty string
        $resumptionToken = '';

        // Set the operation type
        $operationType = 'new';

        // clear the session token
        session(['resumptionToken' => '']);

        // Return the view with the necessary variables
        return view('operations.retrieve_new_items', compact('actionRoute', 'isFinished', 'selectedItemCount', 'resumptionToken', 'operationType'));
    }


    /**
     * Retrieve and process new items from the Analytics XML Report.
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View|\Illuminate\Http\RedirectResponse
     */
    public function retrieveNewItems(Request $request)
    {
        try {
            // Retrieve the selected item count from the form submission
            $itemCount = $request->input('itemCount');

            // Retrieve the operation type from the form submission
            $operationType = $request->input('operationType');

            // Log the operation type
            Log::info('Start operation type: ' . $operationType);

            // Fetch XML data based on operation type and parameters
            if ($operationType === 'continue') {
                $lastRetrieveOperation = Operation::where('operation_type', 'new')->orderBy('id', 'desc')->first();
                if ($lastRetrieveOperation) {
                    $itemCount = $lastRetrieveOperation->item_count;
                } else {
                    $itemCount = $this->defaultItemCount;
                }
                Log::debug('itemCount for continue: '. $itemCount);
                // For "continue" operation, build query with resumptionToken and itemCount
                $resumptionToken = $request->input('resumptionToken');
                $xmlData = $this->fetchXmlService->fetchData($operationType, $itemCount, $resumptionToken);
            } else {
                Log::debug('itemCount for new: '. $itemCount);
                // For "new" operation, build query with itemCount only
                $xmlData = $this->fetchXmlService->fetchData($operationType, $itemCount, null);
                // Extract the resumption token from the XML data
                $resumptionToken = $this->extractResumptionToken($xmlData);
            }

            // Parse XML data
            $retrievedItems = $this->itemImportService->processXmlData($xmlData,$operationType);

            // Store the operation details in the operations table
            $operation = Operation::create([
                'operation_name' => 'Retrieve New Items',
                'item_count' => count($retrievedItems),
                'operation_type' => $operationType,
            ]);

            // Pass operation ID to the item repository for storing items
            $this->itemRepository->addOrUpdateItems($retrievedItems, $operation->id);

            // Retrieve items updated during the current operation
            $updatedItems = Item::where('last_operation_id', $operation->id)->get();

            // Parse XML data to extract the IsFinished flag
            $isFinished = $this->parseIsFinishedFlag($xmlData);

            // Return the view with the variables
            $actionRoute = route('retrieve-new-items');
            return view('operations.retrieve_new_items', [
                'items' => $updatedItems,
                'selectedItemCount' => $itemCount,
                'actionRoute' => $actionRoute,
                'isFinished' => $isFinished,
                'resumptionToken' => $resumptionToken,
                'operationType' => 'continue'
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching or processing data: ' . $e->getMessage());
            // Flash error message
            Flash::error('Error fetching or processing data: ' . $e->getMessage());
            // Redirect back to the retrieve new items page
            return redirect()->route('retrieve-new-items', ['isFinished' => true, 'resumptionToken' => null]);
        }
    }


    /**
     * Parses the XML data to extract the IsFinished flag.
     *
     * @param string $xmlData
     * @return string
     * @throws Exception
     */
    private function parseIsFinishedFlag(string $xmlData): string
    {
        $xml = new SimpleXMLElement($xmlData);
        $isFinished = isset($xml->QueryResult->IsFinished) ? (string) $xml->QueryResult->IsFinished : 'true';

        return $isFinished;
    }


    /**
     * Extracts the resumption token from the XML data and sets it in the session.
     *
     * @param string|null $xmlData
     * @return string|null The extracted resumption token or null if not found.
     */
    private function extractResumptionToken(?string $xmlData)
    {
        if (!$xmlData) {
            return null;
        }

        try {
            $xml = new SimpleXMLElement($xmlData);
            $queryResult = $xml->QueryResult ?? null;
            if (!$queryResult) {
                return null;
            }
            $resumptionToken = isset($queryResult->ResumptionToken) ? (string) $queryResult->ResumptionToken : null;

            // Set the resumption token in the session
            if ($resumptionToken) {
                session(['resumptionToken' => $resumptionToken]);
            }

            return $resumptionToken;
        } catch (Exception $e) {
            Log::error('Error extracting resumption token: ' . $e->getMessage());
            return null;
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
                } else {
                    // Mark the item as failed
                    $item->status = 'failed';
                }
                $item->last_operation_id = $operation->id;
                $item->save();
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
