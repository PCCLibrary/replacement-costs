@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Retrieve New Items</h1>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body">
                @include('components.item_count_form', ['selectedItemCount' => $selectedItemCount])
            </div>
        </div>

        <!-- Display retrieved items (if any) -->
        @include('components.retrieved_items')
    </div>


@endsection
