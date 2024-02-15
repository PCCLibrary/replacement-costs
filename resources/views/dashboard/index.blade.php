@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-6">

                <div class="col-12">
                <h2>Current Status</h2>
                </div>

            <div class="col-12">
                @include('components.dashboard_box', [
                'bgClass' => 'bg-success',
                'icon' => 'fa fa-book',
                'infoBoxText' => 'New Imported Items',
                'count' => $newItemsCount
            ])
            </div>
            <div class="col-12">
                @include('components.dashboard_box', [
                'bgClass' => 'bg-primary',
                'icon' => 'fa fa-check',
                'infoBoxText' => 'Processed Items',
                'count' => $processedItemsCount
            ])
            </div>
            <div class="col-12">
                @include('components.dashboard_box', [
                'bgClass' => 'bg-danger',
                'icon' => 'fa fa-exclamation-triangle',
                'infoBoxText' => 'Failed Items',
                'count' => $failedItemsCount
            ])
            </div>

            </div>

            <div class="col-md-6">

                <h2>History</h2>

                <div class="card bg-lightblue">
                    <div class="card-header">
                        <h3 class="card-title"><span class="fa fa-calendar"></span> Last XML Import</h3>
                    </div>
                    <div class="card-body">
                        <p>Items imported from the XML Alma analytics report.</p>
                        February 09, 4:40 PM
                    </div>

                </div>

                <div class="card bg-teal">
                    <div class="card-header">
                        <h3 class="card-title"><span class="fa fa-clock"></span> Last Process Batch</h3>
                    </div>
                    <div class="card-body">
                        <p>Items updated in the ALMA API.</p>
                        February 10, 4:40 PM
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
