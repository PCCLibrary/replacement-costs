<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Item;
use App\Repositories\ItemRepository;

class HomeController extends Controller
{
    protected $itemRepository;

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
        // Get the count of items
        $newItemsCount = $this->itemRepository->countItemsByStatus('new');

        $processedItemsCount = $this->itemRepository->countItemsByStatus('processed');

        $failedItemsCount = $this->itemRepository->countItemsByStatus('failed');

        return view('dashboard.index', [
            'newItemsCount' => $newItemsCount,
            'processedItemsCount' => $processedItemsCount,
            'failedItemsCount' => $failedItemsCount
        ]);
    }

}
