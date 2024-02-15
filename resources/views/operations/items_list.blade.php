@extends('layouts.app.blade')

@section('content')

        <h1>Imported Items</h1>
        <p class="lead-description">
            Items previously imported from Replacement Costs Analytics Report: <br/>
            <i>/shared/Portland Community College/Reports/Tech Services Reports/mdw reports/mdw-replacement cost examples</i>
        </p>
        <table class="table">
            <thead>
            <tr>
                <th>Title</th>
                <th>MMS ID</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item['Title'] }}</td>
                    <td>{{ $item['MMS Id'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

@endsection
