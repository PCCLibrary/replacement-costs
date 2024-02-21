@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Retrieve New Items</h1>
                </div>
            </div>
            <div class="row">
                <div class="col">
                <p class="lead">Retrieve new items from the Alma Analytics XML Report.</p>
                    <p>Items are retrieved from the Alma analytics <strong>mdw-items without Replacement</strong> report. After retrieval, items are then stored in the application database on the Library server. You then have the option to continue retrieving additional items, with the same item count.</p>
                <p>After retrieval, the items will need to be processed to update relevant data, such as replacement costs. Click the <strong>Go to Process Items</strong> button to navigate to that screen.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body">

            @if($isFinished == 'false' && $operationType == 'continue')
                    @include('components.continue_retrieval_button')
                @else
                    @include('components.item_count_form', [
                                'selectedItemCount' => $selectedItemCount,
                                'operation' => 'Retrieve',
                                'actionRoute' => $actionRoute
                                ])
                @endif
            </div>

        </div>

        @include('components.spinner')

        @include('components.retrieved_items_table')

    </div>

@endsection
