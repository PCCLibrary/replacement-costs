@extends('layouts.app')

@section('content')
    <div class="container-fluid">

                <div class="row py-4">
                    <div class="col-12">
                        <h2>Current Status</h2>
                    </div>
                    <div class="col-4">
                        @include('components.dashboard_box', [
                        'bgClass' => 'bg-success',
                        'icon' => 'fa fa-book',
                        'infoBoxText' => 'New Imported Items',
                        'count' => $newItemsCount
                        ])
                    </div>
                    <div class="col-4">
                        @include('components.dashboard_box', [
                        'bgClass' => 'bg-primary',
                        'icon' => 'fa fa-check',
                        'infoBoxText' => 'Processed Items',
                        'count' => $processedItemsCount
                        ])
                    </div>
                    <div class="col-4">
                        @include('components.dashboard_box', [
                        'bgClass' => 'bg-danger',
                        'icon' => 'fa fa-exclamation-triangle',
                        'infoBoxText' => 'Failed Items',
                        'count' => $failedItemsCount
                        ])
                    </div>
                </div>


                @include('components.history_table', ['latestOperations' => $latestOperations])
    </div>
@endsection
