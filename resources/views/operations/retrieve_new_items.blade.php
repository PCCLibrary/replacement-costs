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
                    <p class="lead">Retrieve new items from the Alma Analytics report.</p>
                    <p>Set the number of items to fetch using the form below. Alma analytics only supports increments of
                        25 items at a time. These items are retrieved as part of an XML file, have the fields mapped
                        with the labels from the XML schema, and are saved to the system database.</p>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body">
                @include('components.item_count_form', [
                        'selectedItemCount' => $selectedItemCount,
                        'operation' => 'Retrieve',
                        'actionRoute' => $actionRoute
                        ])

                @include('components.continue_retrieval_button')
            </div>

        </div>

        @include('components.retrieved_items_table')

    </div>

@endsection
