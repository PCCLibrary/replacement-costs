@if(isset($items) && count($items) > 0)

    <div style="margin-top: 2em;">
        <!-- Check if there are items to display -->
        <h5>{{ $title }} Items</h5>
        <p class="lead">{{ $info }}</p>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Title</th>
                <th>MMS ID</th>
                <th>Holding ID</th>
                <th>Physical Item ID</th>
                <th>Replacement Costs</th>
                <!-- Add more columns as needed -->
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['bib_data']['title'] }}</td>
                    <td>{{ $item['bib_data']['mms_id'] }}</td>
                    <td>{{ $item['holding_data']['holding_id'] }}</td>
                    <td>{{ $item['item_data']['pid'] }}</td>
                    <td>{{ $item['item_data']['replacement_cost'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
