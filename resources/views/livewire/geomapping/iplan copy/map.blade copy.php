<?php

use App\Models\Commodity;
use Livewire\Attributes\On;
use App\Models\GeoCommodity;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

new class extends Component {
    public string $query = '';
    public array $results = [];
    public float $lat = 12.8797;
    public float $lon = 121.774;
    public $commodities = [];
    public $provinceGeo = [];
    public $temporaryGeo = [];
    public $temporaryForDeletion = [];
    public $interventions = '';
    public $selectedCommodity = null;

    public function mount(): void
    {
        $this->commodities = Commodity::orderBy('name', 'asc')->get();
        $this->provinceGeo = GeoCommodity::where('province_id', 1)->with('commodity')->get()->toArray();
    }

    public function search(): void
    {
        if (strlen($this->query) < 3) {
            $this->results = [];
            return;
        }

        $response = Http::withHeaders([
            'User-Agent' => 'I-REAP_BIDDING (mojicamarcallen@gmail.com)',
        ])->get('https://nominatim.openstreetmap.org/search', [
            'q' => $this->query,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 5,
            'countrycodes' => 'ph',
        ]);

        $data = $response->json();
        $this->results = is_array($data) ? $data : [];
    }

    #[On('updateSelectedCommodity')]
    public function updateSelectedCommodity($value)
    {
        if ($value) {
            $this->selectedCommodity = $value;
        }
    }

    public function addTempCommodity()
    {
        $this->validate([
            'selectedCommodity' => 'required',
            'lat' => 'required',
            'lon' => 'required',
            'interventions' => 'required',
        ]);
        $commodity = Commodity::find($this->selectedCommodity);
        if ($commodity) {
            $this->temporaryGeo[] = [
                'commodity_id' => $this->selectedCommodity,
                'latitude' => $this->lat,
                'longitude' => $this->lon,
                'interventions' => $this->interventions,
                'commodity' => [
                    'id' => $commodity->id,
                    'name' => $commodity->name,
                    'icon' => $commodity->icon,
                ],
            ];

            $this->dispatch('temporaryGeoUpdated', $this->temporaryGeo);
            $this->dispatch('removeMarkers');
            $this->dispatch('resetDropDown');

            $this->selectedCommodity = null;
            $this->interventions = '';
        }
    }

    #[On('deleteTempCommodity')]
    public function deleteTempCommodity($payload)
    {
        $id = $payload['id'] ?? null;
        if (!$id) {
            return;
        }

        if ($payload['isTemp']) {
            $this->temporaryGeo = array_values(
                array_filter($this->temporaryGeo, function ($item) use ($id) {
                    return $item['commodity']['id'] != $id;
                }),
            );
            $this->dispatch('temporaryGeoUpdated', $this->temporaryGeo);
        } else {
            array_push($this->temporaryForDeletion, $id);
            $this->provinceGeo = GeoCommodity::where('province_id', 1)->whereNotIn('id', $this->temporaryForDeletion)->with('commodity')->get()->toArray();
            $this->dispatch('provinceGeoUpdated', $this->provinceGeo);
        }
    }

    public function saveUpdates()
    {
        foreach ($this->temporaryGeo as $geo) {
            GeoCommodity::create([
                'commodity_id' => $geo['commodity_id'],
                'latitude' => $geo['latitude'],
                'longitude' => $geo['longitude'],
                'province_id' => 1,
            ]);
        }
        foreach ($this->temporaryForDeletion as $key => $id) {
            GeoCommodity::find($id)->delete();
        }
        $this->temporaryForDeletion = [];
        $this->temporaryGeo = [];
        $this->lat = 0;
        $this->lon = 0;
        LivewireAlert::title('Updated!')->text('The commodities entries have been updated.')->success()->toast()->position('top-end')->show();
    }
};
?>
<div class="row">
    <!-- Left Sidebar -->
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white fw-bold">
                Search Location
            </div>
            <div class="card-body">
                <div wire:ignore x-data="window.mapSearch(@js($provinceGeo), @js($temporaryGeo))" x-init="initMap()">

                    <!-- Search Input -->
                    <input x-model="query" @input.debounce.500="onInput" type="text" class="form-control mb-2"
                        placeholder="Search for a place in PH..." autocomplete="off">

                    <!-- Dropdown Results -->
                    <div x-show="open && results.length" class="search-results border rounded bg-white mb-2"
                        style="max-height: 200px; overflow-y: auto;">
                        <template x-for="(res, idx) in results" :key="idx">
                            <div @click="selectResult(res)" class="p-2 cursor-pointer border-bottom hover:bg-light"
                                :title="res.display_name" style="cursor:pointer">
                                <span x-text="res.display_name"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Lat & Long Display -->
                    <div x-show="hasMarker" class="mt-2 small text-muted">
                        <div class="row">
                            <strong>Latitude:</strong> <span x-text="$wire.lat.toFixed(6)"></span><br>
                            <strong>Longitude:</strong> <span x-text="$wire.lon.toFixed(6)"></span>
                        </div>
                        <div class="row" wire:ignore>
                            <label for="commodity-dropdown" class="col-form-label">Commodity <span
                                    class="text-danger">*</span> </label>
                            <select id="commodity-dropdown" class="form-select" name="state" style="width:100%"
                                placeholder="Select Commodity">
                                <option></option>
                                @foreach ($commodities as $commodity)
                                    <option value="{{ $commodity->id }}">{{ $commodity->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="intervention" class="col-form-label">Interventions <span
                                    class="text-danger">*</span> </label>
                            <textarea class="form-control" wire:model='interventions' rows="5" placeholder="Please enter interventions">

                           </textarea>
                        </div>
                        <div class="text-end mt-4">
                            <button class="btn btn-success" wire:click="addTempCommodity"><i
                                    class="bi bi-plus-circle me-2"></i>Add Commodity</button>
                        </div>
                    </div>
                    <div class="small text-muted">
                        <div class="mt-4 ">
                            <button class="btn btn-primary  text-center w-100" wire:click="saveUpdates">Save
                                Updates</button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Map Column -->
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white fw-bold">
                Map Viewer
            </div>
            <div class="card-body p-0">
                <div wire:ignore id="map"></div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        $(document).ready(function() {
            const select = $('#commodity-dropdown');
            select.select2({
                theme: 'bootstrap-5',
                placeholder: 'Select Commodity',
                allowClear: true
            });

            // Listen to change and update Livewire manually
            select.on('change', function(e) {
                let selectedValue = $(this).val();
                Livewire.dispatch('updateSelectedCommodity', {
                    value: selectedValue
                });
            });
            Livewire.on('resetDropDown', () => {
                const select = $('#commodity-dropdown');
                select.val(null).trigger('change');
            });
        });

        window.mapSearch = function(provinceGeo, temporaryGeo) {
            return {
                query: '',
                results: [],
                open: false,
                map: null,
                marker: null,
                hasMarker: false,
                selectedLabel: '',
                lat: 12.8797,
                lon: 121.7740,
                provinceGeo,
                temporaryGeo,
                markersProvince: [],
                markersTemporary: [],

                initMap() {
                    this.map = L.map('map').setView([this.lat, this.lon], 6);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                    }).addTo(this.map);

                    this.addProvinceMarkers();
                    this.addTemporaryMarkers();

                    // Listen for Livewire updates to temporaryGeo
                    Livewire.on('temporaryGeoUpdated', (newTemporaryGeo) => {
                        console.log('Raw temporaryGeo:', newTemporaryGeo);
                        this.temporaryGeo = newTemporaryGeo.flat ? newTemporaryGeo.flat() : newTemporaryGeo;
                        this.addTemporaryMarkers();
                    });

                    Livewire.on('provinceGeoUpdated', (newProvinceGeo) => {
                        console.log('Raw provinceGeo:', newProvinceGeo);
                        this.provinceGeo = newProvinceGeo.flat ? newProvinceGeo.flat() : newProvinceGeo;
                        this.addProvinceMarkers();
                    });

                    Livewire.on('removeMarkers', () => {
                        this.lat = 0;
                        this.lon = 0;
                        this.placeMarker(0, 0);
                    });


                    // Map click for manual pin
                    this.map.on('click', (e) => {
                        const {
                            lat,
                            lng
                        } = e.latlng;
                        this.lat = lat;
                        this.lon = lng;
                        this.$wire.set('lat', lat);
                        this.$wire.set('lon', lng);
                        this.reverseGeocode(lat, lng, true);
                    });
                },

                addProvinceMarkers() {
                    // Remove old markers
                    this.markersProvince.forEach(m => this.map.removeLayer(m));
                    this.markersProvince = [];

                    this.provinceGeo.forEach(entry => {
                        if (entry.latitude && entry.longitude && entry.commodity) {
                            const icon = this.createIcon(entry.commodity.icon);
                            const marker = L.marker([entry.latitude, entry.longitude], {
                                    icon
                                })
                                .addTo(this.map)
                                .bindPopup(`
                    <div class="text-center" style="min-width: 150px;">
                        <div class="fw-bold mb-2 text-primary">
                            ${entry.commodity.name}
                        </div>
                        <div class="small text-muted my-2" style="text-align: justify">${entry.interventions}</div>
                        <hr>
                        <div class="d-flex justify-content-center">
                        <button onclick="window.deleteTempCommodity(${entry.id}, 0)"
                            class="btn btn-sm btn-outline-danger btn-icon d-flex align-items-center gap-1">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                        </div>
                    </div>
                `);
                            this.markersProvince.push(marker);
                        }
                    });
                },

                addTemporaryMarkers() {
                    // Remove old temporary markers
                    this.markersTemporary.forEach(m => this.map.removeLayer(m));
                    this.markersTemporary = [];

                    // Flatten in case of nested array
                    const geoPoints = Array.isArray(this.temporaryGeo[0]) ? this.temporaryGeo.flat() : this
                        .temporaryGeo;

                    geoPoints.forEach(entry => {
                        if (entry.latitude && entry.longitude && entry.commodity) {
                            const icon = this.createIcon(entry.commodity.icon);
                            const marker = L.marker([entry.latitude, entry.longitude], {
                                    icon
                                })
                                .addTo(this.map)
                                .bindPopup(`
                    <div class="text-center" style="min-width: 150px;">
                        <div class="fw-bold mb-2 text-primary">
                            ${entry.commodity.name} (Temporary)
                        </div>
                       <div class="small text-muted my-2" style="text-align: justify">${entry.interventions}</div>
                        <hr>
                        <div class="d-flex justify-content-center">
                        <button onclick="window.deleteTempCommodity(${entry.commodity.id}, 1)"
                            class="btn btn-sm btn-outline-danger btn-icon d-flex align-items-center gap-1">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                        </div>
                    </div>
                `);
                            this.markersTemporary.push(marker);
                        }
                    });
                },

                createIcon(iconPath, commodityId = null) {
                    const finalUrl = iconPath.startsWith('http') ? iconPath : `/icons/${iconPath}`;

                    return L.divIcon({
                        className: 'custom-marker-icon position-relative',
                        html: `
            <div class="marker-circle" >
                <img src="${finalUrl}" alt="Icon"
                     onerror="this.onerror=null;this.src='/icons/icons/default.png';"
                     style="width: 32px; height: 32px; border-radius: 50%;"/>
            </div>
        `,
                        iconSize: [32, 32],
                        iconAnchor: [16, 32],
                        popupAnchor: [0, -32]
                    });
                },


                onInput() {
                    if (this.query.length < 3) {
                        this.results = [];
                        this.open = this.hasMarker;
                        return;
                    }

                    this.$wire.set('query', this.query);
                    this.$wire.search().then(() => {
                        this.results = this.$wire.results;
                        this.open = this.results.length > 0 || this.hasMarker;
                    });
                },

                selectResult(res) {
                    this.lat = parseFloat(res.lat);
                    this.lon = parseFloat(res.lon);
                    this.$wire.set('lat', this.lat);
                    this.$wire.set('lon', this.lon);
                    this.query = res.display_name;
                    this.selectedLabel = res.display_name;
                    if (!this.map) {
                        console.warn('Map is not initialized yet.');
                        return;
                    }

                    this.map.setView([this.lat, this.lon], 14);
                    this.placeMarker(this.lat, this.lon, res.display_name);

                    this.open = false;
                    this.results = [];
                },

                placeMarker(lat, lon, label = '') {
                    if (lat === 0 && lon === 0) {
                        if (this.marker) {
                            this.map.removeLayer(this.marker);
                            this.marker = null;
                            this.hasMarker = false;
                        }
                        return; // Exit early
                    }
                    if (this.marker) {
                        this.marker.setLatLng([lat, lon]);
                        this.marker.setPopupContent(label || 'Pinned Location');
                    } else {
                        this.marker = L.marker([lat, lon], {
                                draggable: true
                            }).addTo(this.map)
                            .bindPopup(label || 'Pinned Location')
                            .openPopup();

                        this.marker.on('dragend', (e) => {
                            const newLatLng = e.target.getLatLng();
                            this.lat = newLatLng.lat;
                            this.lon = newLatLng.lng;
                            this.$wire.set('lat', this.lat);
                            this.$wire.set('lon', this.lon);

                            this.reverseGeocode(this.lat, this.lon, true);
                        });
                    }

                    this.hasMarker = true;
                    this.selectedLabel = label;
                    this.marker.openPopup();
                },

                reverseGeocode(lat, lon, updateMap = false) {
                    fetch(
                            `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json&addressdetails=1`
                        )
                        .then(res => res.json())
                        .then(data => {
                            const name = data.display_name || `Lat: ${lat.toFixed(5)}, Lng: ${lon.toFixed(5)}`;
                            this.query = name;
                            this.selectedLabel = name;

                            this.results = [];
                            this.open = false;

                            if (updateMap) {
                                this.map.setView([lat, lon], this.map.getZoom());
                                this.placeMarker(lat, lon, name);
                            }
                        })
                        .catch(err => {
                            console.error("Reverse geocoding failed:", err);
                            const fallback = `Lat: ${lat.toFixed(5)}, Lng: ${lon.toFixed(5)}`;
                            this.query = fallback;
                            this.selectedLabel = fallback;

                            this.results = [];
                            this.open = false;

                            if (updateMap) {
                                this.placeMarker(lat, lon, fallback);
                            }
                        });
                }
            }
        }
        window.deleteTempCommodity = function(commodityId, isTemp) {
            if (!commodityId) return;
            Livewire.dispatch('deleteTempCommodity', {
                payload: {
                    id: commodityId,
                    isTemp: isTemp
                }
            });
        }
    </script>
@endscript
