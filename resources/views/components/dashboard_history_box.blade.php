<div class="card {{ $bgColor ?? 'bg-lightblue' }}">
    <div class="card-header">
        <h3 class="card-title">
            <span class="{{ $icon }}"></span> {{ $title }}
        </h3>
    </div>
    <div class="card-body">
    <div class="row">
        <div class="col-5">
            <p><strong>{{ $date }}</strong></p>
        </div>
        <div class="col-7">
            <p>{{ $count }} {{ $description }}</p>
        </div>
    </div>
    </div>
</div>
