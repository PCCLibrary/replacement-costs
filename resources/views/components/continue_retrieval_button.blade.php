@if($isFinished == 'false')
    <div class="text-mt-3 mt-4 alert-light p-4">
        <form class="d-flex justify-content-start align-items-center" action="{{ route('retrieve-new-items') }}" method="POST">

            <input type="hidden" name="operationType" value="continue">

            @csrf <!-- Add CSRF token for CSRF protection -->
            <input type="hidden" name="resumptionToken" value="{{ $resumptionToken  }}">
            <input type="hidden" name="isFinished" value="{{ $isFinished }}">
            <label class="p-0 m-0 mr-4" for="continue-button">There are still items in the report. Continue?</label>
            <button id="continue-button" type="submit" class="btn btn-primary m-0 mr-4">Continue Retrieval</button>
            <a href="{{ route('process-items') }}" class="btn btn-success">Go to Process Items</a>
        </form>
    </div>
@endif
