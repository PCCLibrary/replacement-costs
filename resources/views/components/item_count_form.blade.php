<form action="{{ $actionRoute }}" method="POST" class="row g-3 align-items-center">
    @csrf
    <div class="col-12 col-md-2">
        <label for="itemCount" class="col-form-label">Number of Items to {{ $operation }}:</label>
    </div>
    <div class="col-12 col-md-4">
        <select class="form-control" id="itemCount" name="itemCount">
            <option value="5" {{ $selectedItemCount == 5 ? 'selected' : '' }}>5</option>
            <option value="25" {{ $selectedItemCount == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ $selectedItemCount == 50 ? 'selected' : '' }}>50</option>
            <option value="75" {{ $selectedItemCount == 75 ? 'selected' : '' }}>75</option>
            <option value="100" {{ $selectedItemCount == 100 ? 'selected' : '' }}>100</option>
            <option value="125" {{ $selectedItemCount == 125 ? 'selected' : '' }}>125</option>
            <option value="150" {{ $selectedItemCount == 150 ? 'selected' : '' }}>150</option>
            <option value="175" {{ $selectedItemCount == 175 ? 'selected' : '' }}>175</option>
            <option value="200" {{ $selectedItemCount == 200 ? 'selected' : '' }}>200</option>
        </select>
    </div>
    <div class="col-12 col-md-6">
        <button type="submit" class="btn btn-primary">{{ $operation }} Items</button>
    </div>
</form>
