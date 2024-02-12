<?php

namespace App\Http\Controllers;

use App\Repositories\ItemRepository;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 *
 */
class CleanUpController extends Controller
{
    /**
     * @var ItemRepository
     */
    protected ItemRepository $itemRepo;

    /**
     * @param ItemRepository $itemRepo
     */
    public function __construct(ItemRepository $itemRepo)
    {
        $this->itemRepo = $itemRepo;
    }

    /**
     * Display the cleanup page.
     *
     * @return View
     */
    public function showCleanUpPage()
    {
        Log::debug('Accessed showCleanUpPage method.');
        return view('cleanup');
    }

    /**
     * Handle the cleanup process.
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function handleCleanUp()
    {
        $this->itemRepo->deleteAllItems();

        return redirect()->route('show_clean_up')->with('success', 'Items have been deleted successfully.');
    }
}
