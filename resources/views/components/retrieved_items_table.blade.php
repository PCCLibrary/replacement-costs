@if(isset($items) && count($items) > 0)
{{--    <div class="alert alert-info p-4 ">--}}
{{--        <!-- Check if there are items to display -->--}}
{{--        <div class="lead p-0 m-0">{{ count($items) }} new items saved to the database.</div>--}}
{{--    </div>--}}

    <table class="table table-striped mt-4">
        <thead>
        <tr>
            <th>Title</th>
            <th>MMS ID</th>
            <th>HID</th>
            <th>PID</th>
            <th>Cost</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item['title'] }}</td>
                <td>{{ $item['mms_id'] }}</td>
                <td>{{ $item['holding_id'] }}</td>
                <td>{{ $item['physical_item_id'] }}</td>
                <td>{{ $item['replacement_cost'] }}</td>
                <td>{{ date('M d, Y h:i', strtotime($item['created_at'])) }}</td>
                <td>
                        <span class="badge {{ $item['status'] === 'new' ? 'bg-success' : ($item['status'] === 'processed' ? 'bg-info' : 'bg-warning') }}">
                            {{ $item['status'] }}
                        </span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
