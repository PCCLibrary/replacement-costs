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
                <!-- Form for retrieving new items -->
                <form action="{{ route('retrieve-new-items') }}" method="POST" class="row g-3 align-items-center">
                    @csrf
                    <div class="col-12 col-md-2">
                        <label for="itemCount" class="col-form-label">Number of Items to Retrieve:</label>
                    </div>
                    <div class="col-12 col-md-4">
                        <select class="form-control" id="itemCount" name="itemCount">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="75">75</option>
                            <option value="100">100</option>
                            <option value="125">125</option>
                            <option value="150">150</option>
                            <option value="175">175</option>
                            <option value="200">200</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <button type="submit" class="btn btn-primary">Retrieve Items</button>
                    </div>
                </form>
            </div>
        </div>



        <!-- Display retrieved items (if any) -->
            @include('components.retrieved_items')
    </div>
@endsection
