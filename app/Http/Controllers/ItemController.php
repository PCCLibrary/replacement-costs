<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Repositories\ItemRepository;
use Illuminate\Contracts\View\View;
use Laracasts\Flash\Flash;

class ItemController extends Controller
{
    protected $itemRepository;

    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function index()
    {
        $items = $this->itemRepository->all();

        return view('items.index')->with('items', $items);
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(CreateItemRequest $request)
    {
        $input = $request->all();
        $item = $this->itemRepository->create($input);

        Flash::success('Item saved successfully.');

        return redirect(route('items.index'));
    }

    public function show($id)
    {
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            Flash::error('Item not found');

            return redirect(route('items.index'));
        }

        return view('items.show')->with('item', $item);
    }

    public function edit($id)
    {
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            Flash::error('Item not found');

            return redirect(route('items.index'));
        }

        return view('items.edit')->with('item', $item);
    }

    public function update(UpdateItemRequest $request, $id)
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
