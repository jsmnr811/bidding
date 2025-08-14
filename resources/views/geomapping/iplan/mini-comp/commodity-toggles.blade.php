<div class="card shadow-sm p-4 h-100 ">
    <h5 class="fs-6 mb-3 fw-bold">🗺️ Toggle Map Layers</h5>
    <hr class="mt-2">
    <div class="row row-cols-1 row-cols-md-2 g-2 " wire:ignore>
        @foreach ($commodities as $commodity)
            <div class="col">
                <div class="form-check form-switch d-flex align-items-center">
                    <input class="form-check-input" type="checkbox" id="commodity-{{ $commodity->id }}"
                        wire:model.live="selectedFilterCommoditites" value="{{ $commodity->id }}">
                    <label class="form-check-label ms-2 d-flex align-items-center" for="commodity-{{ $commodity->id }}">
                        <img class="marker-circle me-2" src="{{ asset('icons/' . $commodity->icon) }}"
                            onerror="this.onerror=null;this.src='{{ asset('icons/icons/default.png') }}';"
                            alt="{{ $commodity->name }}" width="30" height="30" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="{{ $commodity->name }}">
                    </label>
                </div>
            </div>
        @endforeach
    </div>
</div>
