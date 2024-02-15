<div class="table-responsive">
    <table class="table" id="items-table">
        <thead>
        <tr>
            <th>Title</th>
            <th>MMS ID</th>
            <th>Holding ID</th>
            <th>Physical Item ID</th>
            <th>Replacement Cost</th>
            <th>Status</th>
            <th colspan="3">Action</th>
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
                <td>{{ $item['status'] }}</td>
                <td width="120">
                    {!! Form::open(['route' => ['items.destroy', $item->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('items.show', [$item->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('items.edit', [$item->id]) }}" class='btn btn-default btn-xs'>
                            <i class="far fa-edit"></i>
                        </a>
                        {!! Form::button('<i class="far fa-trash-alt"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
