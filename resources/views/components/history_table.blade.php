<div class="row py-4">
    <div class="col-12">
        <h2>History</h2>
            @if(!isset($latestOperations) && count($latestOperations) > 0)
                <p>No operations</p>
            @else
                <table class="table table-striped">
                    <thead>
                    <tr class="bg-info text-white">
                        <th>Date</th>
                        <th>Operation</th>
                        <th>Item Count</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($latestOperations as $operation)
                        <tr>
                            <td>{{ $operation->updated_at }}</td>
                            <td>{{ $operation->operation_name }}</td>
                            <td>{{ $operation->item_count }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
    </div>
</div>
