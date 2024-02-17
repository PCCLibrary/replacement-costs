<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Models\Item;
use App\Repositories\ItemRepository;
use App\Models\Operation;


class HomeController extends Controller
{
    protected ItemRepository $itemRepository;

    public function __construct(ItemRepository $itemRepository)
    {
        $this->middleware('auth');
        $this->itemRepository = $itemRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
//        // Get the latest retrieve operation
//        $latestRetrieveOperation = Operation::where('operation_type', 'retrieve')
//            ->latest()
//            ->first();
//
//        $latestProcessOperation = Operation::where('operation_type', 'process')
//            ->latest()
//            ->first();

        // Get the latest operations
        $latestOperations = Operation::latest()->take(5)->get();

        Log::info('Operation Items: ' . $latestOperations->toJson());


        // Get the count of items
        $newItemsCount = $this->itemRepository->countItemsByStatus('new');

        $processedItemsCount = $this->itemRepository->countItemsByStatus('processed');

        $failedItemsCount = $this->itemRepository->countItemsByStatus('failed');

        return view('dashboard.index', [
            'newItemsCount' => $newItemsCount,
            'processedItemsCount' => $processedItemsCount,
            'failedItemsCount' => $failedItemsCount,
            'latestOperations' => $latestOperations
        ]);
    }

}
