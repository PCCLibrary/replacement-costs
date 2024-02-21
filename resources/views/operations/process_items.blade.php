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
                    <p class="lead">Update replacement costs in Alma.</p>
                    <p>Previously retrieved, stored items in the database, with the status of 'new' are processed, and then the updated items are saved in Alma using the Holdings API.</p>
                    <p><strong>Note:</strong> This is a <i>slow</i> operation. Do not navigate away until the operation is done. 100 items takes about 8 minutes.</p>
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
                        'operationType' => 'process',
                        'actionRoute' => $actionRoute
                        ])
            </div>
        </div>

        @include('components.spinner')

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
