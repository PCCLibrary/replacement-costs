@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Process Items</h1>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <p class="lead">Process items with the Alma API to update replacement costs.</p>
                    <p>Items with the status of 'new' are read from the application database, and used with the Alma
                        holdings API to retrieve an Alma item. The replacement_cost field is updated, the data is
                        reformatted, and then the change is saved in Alma using the Holdings API.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body">
                @include('components.item_count_form',
                        ['selectedItemCount' => $selectedItemCount,
                        'operation' => 'Process',
                        'actionRoute' => $actionRoute
                        ])
            </div>
        </div>


        @if(isset($processedItems) && count($processedItems) > 0)
            @include('components.items_table', [
                'items' => $processedItems,
                'title' => 'Processed Items',
                'description' => 'successfully updated in the Alma API.'
            ])
        @endif

        @if(isset($failedItems) && count($failedItems) > 0)
            @include('components.items_table', [
                'items' => $failedItems,
                'title' => 'Failed Items',
                'description' => 'failed to retrieve or update in the Alma API.'
            ])
        @endif


    </div>

@endsection
