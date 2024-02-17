@if(isset($items) && count($items) > 0)
    <div class="bg-light p-4 border-bottom">
        <h5>{{ $title }}</h5>
        <p class="lead p-0 m-0">{{ count($items) }} Items {{ $description }}</p>
    </div>

    <table class="table table-striped">
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
                <td>{{ $item->title }}</td>
                <td>{{ $item->mms_id }}</td>
                <td>{{ $item->holding_id }}</td>
                <td>{{ $item->physical_item_id }}</td>
                <td>{{ $item->replacement_cost }}</td>
                <td>{{ $item->created_at }}</td>
                <td>
                    <span class="badge {{ $item->status === 'new' ? 'bg-success' : ($item->status === 'processed' ? 'bg-info' : 'bg-warning') }}">
                        {{ $item->status }}
                    </span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
