@extends('layouts.app.blade')

@section('content')
    @if(!isset($retrievedItems) && !isset($failedItems))
        <h5>No Items Found</h5>
        <p>No items in the database. Retrieve new items from the Alma analytics report.</p>
        <a class="btn btn-primary" href="{{ route('items.retrieve') }}">Retrieve New Items</a>
    @else
        <h5>Process Items</h5>
        <form method="GET" action="{{ route('items.handle_form') }}">
            <div class="form-group">
                <label for="itemCount">Number of Items to Process:</label>
                <input type="number" class="form-control" id="itemCount" name="count" value="{{ old('count', 10) }}">
            </div>
            <button type="submit" class="btn btn-primary">Process Items</button>
        </form>

        {{-- If there are retrieved items, display them --}}
        @if (isset($retrievedItems) && count($retrievedItems) > 0)
            @include('components.retrieved_items', ['title' => 'Retrieved','items' => $retrievedItems])
        @else
            <div class="card" style="margin-top: 2em;">
                <div class="card-body"><b>No retrieved items.</b></div>
            </div>
        @endif

        {{-- If there are updated items, display them --}}
        @if (isset($updatedItems) && count($updatedItems) > 0)
            @include('components.item_table', ['title' => 'Updated', 'info' => 'Items updated with the Alma API', 'items' => $updatedItems])
        @else
            <div class="card" style="margin-top: 2em;">
                <div class="card-body"><b>No updated items.</b></div>
            </div>
        @endif

        {{-- If there are failed items, display them --}}
        @if (isset($failedItems) && count($failedItems) > 0)
            @include('components.item_table', ['title' => 'Failed', 'info' => 'Items failed to update with the Alma API', 'items' => $failedItems])
        @else
            <div class="card" style="margin-top: 2em;">
                <div class="card-body"><b>No failed items.</b></div>
            </div>
        @endif
    @endif
@endsection
