@if(isset($retrievedItems) && count($retrievedItems) > 0)
    <div style="margin-top: 2em;">
        <!-- Check if there are items to display -->
        <h5>Retrieved Items</h5>
        <p class="lead">Items retrieved from the Alma API using entries from Alma analytics report.</p>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Title</th>
                <th>MMS ID</th>
                <th>Holding ID</th>
                <th>Physical Item ID</th>
                <th>Replacement Cost</th>
                <th>Status</th> <!-- New column for status -->
            </tr>
            </thead>
            <tbody>
            @foreach($retrievedItems as $item)
                <tr>
                    <td>{{ $item['title'] }}</td>
                    <td>{{ $item['mms_id'] }}</td>
                    <td>{{ $item['holding_id'] }}</td>
                    <td>{{ $item['physical_item_id'] }}</td>
                    <td>{{ $item['replacement_cost'] }}</td>
                    <td>{{ $item['status'] }}</td> <!-- Display status -->
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
