<!-- MMS ID Field -->
<div class="form-group col-sm-6">
    {!! Form::label('mms_id', 'MMS ID:') !!}
    {!! Form::text('mms_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Title Field -->
<div class="form-group col-sm-6">
    {!! Form::label('title', 'Title:') !!}
    {!! Form::text('title', null, ['class' => 'form-control']) !!}
</div>

<!-- Holding ID Field -->
<div class="form-group col-sm-6">
    {!! Form::label('holding_id', 'Holding ID:') !!}
    {!! Form::text('holding_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Library Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('library_name', 'Library Name:') !!}
    {!! Form::text('library_name', null, ['class' => 'form-control']) !!}
</div>

<!-- Barcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('barcode', 'Barcode:') !!}
    {!! Form::text('barcode', null, ['class' => 'form-control']) !!}
</div>

<!-- Creation Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('creation_date', 'Creation Date:') !!}
    {!! Form::text('creation_date', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Lifecycle Field -->
<div class="form-group col-sm-6">
    {!! Form::label('lifecycle', 'Lifecycle:') !!}
    {!! Form::text('lifecycle', null, ['class' => 'form-control']) !!}
</div>

<!-- Physical Item ID Field -->
<div class="form-group col-sm-6">
    {!! Form::label('physical_item_id', 'Physical Item ID:') !!}
    {!! Form::text('physical_item_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Receiving Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('receiving_date', 'Receiving Date:') !!}
    {!! Form::text('receiving_date', null, ['class' => 'form-control']) !!}
</div>

<!-- Replacement Cost Field -->
<div class="form-group col-sm-6">
    {!! Form::label('replacement_cost', 'Replacement Cost:') !!}
    {!! Form::text('replacement_cost', null, ['class' => 'form-control']) !!}
</div>

<!-- Order Line Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('order_line_type', 'Order Line Type:') !!}
    {!! Form::text('order_line_type', null, ['class' => 'form-control']) !!}
</div>

<!-- PO Line Reference Field -->
<div class="form-group col-sm-6">
    {!! Form::label('po_line_reference', 'PO Line Reference:') !!}
    {!! Form::text('po_line_reference', null, ['class' => 'form-control']) !!}
</div>

<!-- Reporting Code First Field -->
<div class="form-group col-sm-6">
    {!! Form::label('reporting_code_first', 'Reporting Code First:') !!}
    {!! Form::text('reporting_code_first', null, ['class' => 'form-control']) !!}
</div>

<!-- Fund Expenditure Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fund_expenditure', 'Fund Expenditure:') !!}
    {!! Form::text('fund_expenditure', null, ['class' => 'form-control']) !!}
</div>

<!-- Number of Items per Fund Field -->
<div class="form-group col-sm-6">
    {!! Form::label('num_of_items_per_fund', 'Number of Items per Fund:') !!}
    {!! Form::text('num_of_items_per_fund', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::select('status', ['new' => 'New', 'processed' => 'Processed', 'failed' => 'Failed'], null, ['class' => 'form-control']) !!}
</div>

