<?php

namespace App\Http\Controllers;


use App\Http\Requests\CreateItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Services\AlmaItemService;
use App\Services\FetchXmlReport;
use App\Services\ItemImportService;
use App\Repositories\ItemRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Laracasts\Flash\Flash;


/**
 * Class ItemController
 * @package App\Http\Controllers
 */
class ItemController extends Controller
{
    /**
     * @var FetchXmlReport
     */
    protected $fetchXmlService;

    /**
     * @var ItemImportService
     */
    protected $itemImportService;

    /**
     * @var ItemRepository
     */
    protected $itemRepository;

    /**
     * @var AlmaItemService
     */
    protected $almaService;

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
     * Display the main dashboard of the application.
     *
     * @return View
     */
    public function dashboard()
    {
        $items = Item::all(); // Retrieve all items from the database.

        return view('dashboard', ['items' => $items]);
    }

    /**
     * Display the form to process items.
     * @return View
     */
    public function showProcessItemsForm()
    {
        return view('operations.retrieve_items');
    }

    /**
     * Display a listing of the items.
     *
     * Fetches XML data from an external API using the FetchXmlReport service class,
     * parses the fetched data, stores it in the database, and then displays the items
     * in a view. Also handles exceptions and errors, providing feedback to the user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Processes items by getting them from the Alma bibs API.
     *
     * @param int $count Number of items to process (default 10).
     * @return View
     */
    public function processItems($count = 10)
    {
        try {
            // Fetch 'new' items from the database up to the defined count
            $dbItems = $this->itemRepository->fetchItemsByStatus('new', $count);

            $retrievedItems = $dbItems; // Items retrieved from the Alma API
            $failedItems = [];    // Items failed to retrieve or update in the Alma API
            $updatedItems = [];   // Items successfully updated in the Alma API

            foreach ($dbItems as $dbItem) {
                try {
                    // Retrieve the Alma item
                    $almaItem = $this->almaService->getAlmaItem($dbItem['mms_id'], $dbItem['holding_id'], $dbItem['physical_item_id']);

                    // Add debugging statement to check data type
                    Log::debug('payload from getAlmaItem: ' . json_encode($almaItem));

                    // Update the Alma item's replacement cost
                    $almaItem = $this->almaService->updateReplacementCost($dbItem, $almaItem);

                    // Attempt to update the Alma item without making any changes
                    $updateResult = $this->almaService->updateAlmaItem($almaItem);

                    if ($updateResult && $updateResult['status'] === 200) {
                        $dbItem->status = 'updated';
                        $updatedItems[] = $almaItem;
                    } else {
                        $failedItems[] = $almaItem;
                        $dbItem->status = 'failed';
                    }

                    $dbItem->save();

                } catch (Exception $e) {
                    $dbItem->status = 'not found';
                    $dbItem->save();
                    Log::error('Failed to find/update item: ' . $e->getMessage());
                }
            }

            // Prepare a feedback message for the user
            session()->flash('message', 'Successfully retrieved and updated ' . count($retrievedItems) . ' items from Alma. Failed for ' . count($failedItems) . ' items.');

            // Display the items in the process_items view
            return view('items.process_items', [
                'retrievedItems' => $retrievedItems,
                'failedItems' => $failedItems,
                'updatedItems' => $updatedItems
            ]);

        } catch (Exception $e) {
            Log::error('Error processing items: ' . $e->getMessage());
            session()->flash('error', 'Error processing items.');
            return view('items.error', ['error' => 'Error processing items.']);
        }
    }

    public function handleUpdateItemsForm(Request $request)
    {
        $count = $request->input('count', 10); // default to 10 if not provided
        return redirect()->route('items.update', ['count' => $count]);
    }

    /**
     * Handle the cleaning up of items from the database table.
     *
     * @return View
     */
    public function cleanUp()
    {
        try {
            // Assuming you have a method in your repository that deletes all items
            $this->itemRepository->deleteAllItems();

            // Inform the user of the successful cleanup
            session()->flash('message', 'All items removed from the database.');

            // Return a confirmation view or redirect to the welcome page
            return view('items.cleanup_success');

        } catch (Exception $e) {
            Log::error('Error deleting items from the database: ' . $e->getMessage());
            session()->flash('error', 'Error deleting items from the database.');

            return view('items.error', ['error' => 'Error deleting items from the database.']);
        }
    }

    // CRUD methods from the new controller

    public function index(Request $request)
    {
        $items = $this->itemRepository->all();

        return view('items.index')
            ->with('items', $items);
    }

    /**
     * Show the form for creating a new Item.
     *
     * @return Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created Item in storage.
     *
     * @param CreateItemRequest $request
     *
     * @return Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateItemRequest $request)
    {
        $input = $request->all();

        $item = $this->itemRepository->create($input);

        Flash::success('Item saved successfully.');

        return redirect(route('items.index'));
    }

    /**
     * Display the specified Item.
     *
     * @param int $id
     *
     * @return Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View
     */
    public function show($id)
    {
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            Flash::error('Item not found');

            return redirect(route('items.index'));
        }

        return view('items.show')->with('item', $item);
    }

    /**
     * Show the form for editing the specified Item.
     *
     * @param int $id
     *
     * @return Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit($id)
    {
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            Flash::error('Item not found');

            return redirect(route('items.index'));
        }

        return view('items.edit')->with('item', $item);
    }

    /**
     * Update the specified Item in storage.
     *
     * @param int $id
     * @param UpdateItemRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, UpdateItemRequest $request)
    {
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            Flash::error('Item not found');

            return redirect(route('items.index'));
        }

        $item = $this->itemRepository->update($request->all(), $id);

        Flash::success('Item updated successfully.');

        return redirect(route('items.index'));
    }

    /**
     * Remove the specified Item from storage.
     *
     * @param int $id
     *
     * @return Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     *
     */
    public function destroy($id)
    {
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            Flash::error('Item not found');

            return redirect(route('items.index'));
        }

        $this->itemRepository->delete($id);

        Flash::success('Item deleted successfully.');

        return redirect(route('items.index'));
    }
}
