<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agri Commodities Map - Landing Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            background: linear-gradient(135deg, #e0e7ff 0%, #fff 100%);
            min-height: 100vh;
        }
        .hero-section {
            padding: 4rem 0 2rem 0;
            text-align: center;
        }
        .map-container {
            height: 400px;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        .search-box {
            max-width: 400px;
            margin: 0 auto 1rem auto;
        }
        .dropdown-section, .checkbox-section {
            margin-bottom: 1rem;
        }
        .checkbox-list {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
        }
        .checkbox-list label {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <section class="hero-section">
            <h1 class="display-5 fw-bold mb-3">Agri Commodities Mapping</h1>
            <p class="lead mb-4">Search or pin a location, select commodities and interventions, and visualize them on the map.</p>
            <div class="search-box input-group mb-3">
                <input type="text" id="locationSearch" class="form-control" placeholder="Search for a location...">
                <button class="btn btn-primary" id="searchBtn" type="button">Search</button>
            </div>
        </section>
        <div class="map-container mb-4" id="map"></div>
        <div class="row justify-content-center">
            <div class="col-md-5 dropdown-section">
                <label for="commodityDropdown" class="form-label">Commodity</label>
                <select class="form-select mb-2" id="commodityDropdown">
                    <option selected disabled>Select commodity</option>
                    <option value="rice">Rice</option>
                    <option value="corn">Corn</option>
                    <option value="banana">Banana</option>
                    <option value="coffee">Coffee</option>
                </select>
            </div>
            <div class="col-md-5 dropdown-section">
                <label for="interventionDropdown" class="form-label">Intervention</label>
                <select class="form-select mb-2" id="interventionDropdown">
                    <option selected disabled>Select intervention</option>
                    <option value="irrigation">Irrigation</option>
                    <option value="training">Training</option>
                    <option value="subsidy">Subsidy</option>
                </select>
            </div>
        </div>
        <div class="checkbox-section text-center">
            <h5 class="mb-3">Toggle Commodities on Map</h5>
            <div class="checkbox-list" id="commodityCheckboxes">
                <label><input type="checkbox" value="rice" checked> Rice</label>
                <label><input type="checkbox" value="corn" checked> Corn</label>
                <label><input type="checkbox" value="banana" checked> Banana</label>
                <label><input type="checkbox" value="coffee" checked> Coffee</label>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- Optional: Leaflet Geosearch (for search box) -->
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <script>
        // Initialize map
        var map = L.map('map').setView([13.41, 122.56], 6); // Centered in the Philippines

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Pin location on map by click
        var marker;
        map.on('click', function(e) {
            if (marker) map.removeLayer(marker);
            marker = L.marker(e.latlng).addTo(map);
        });

        // Geocoder for search
        var geocoder = L.Control.geocoder({
            defaultMarkGeocode: false
        })
        .on('markgeocode', function(e) {
            var bbox = e.geocode.bbox;
            var center = e.geocode.center;
            map.fitBounds(bbox);
            if (marker) map.removeLayer(marker);
            marker = L.marker(center).addTo(map);
        })
        .addTo(map);

        // Search button functionality
        document.getElementById('searchBtn').onclick = function() {
            var query = document.getElementById('locationSearch').value;
            if (query) {
                geocoder.options.geocoder.geocode(query, function(results) {
                    if (results.length > 0) {
                        var result = results[0];
                        map.fitBounds(result.bbox);
                        if (marker) map.removeLayer(marker);
                        marker = L.marker(result.center).addTo(map);
                    }
                });
            }
        };

        // Dummy commodity layers (for toggling)
        var commodityLayers = {
            rice: L.circle([13.5, 122.5], {radius: 20000, color: 'green'}).bindPopup('Rice Area'),
            corn: L.circle([13.2, 122.7], {radius: 20000, color: 'yellow'}).bindPopup('Corn Area'),
            banana: L.circle([13.6, 122.3], {radius: 20000, color: 'orange'}).bindPopup('Banana Area'),
            coffee: L.circle([13.4, 122.8], {radius: 20000, color: 'brown'}).bindPopup('Coffee Area')
        };

        // Add all by default
        Object.values(commodityLayers).forEach(layer => layer.addTo(map));

        // Checkbox toggling
        document.querySelectorAll('#commodityCheckboxes input[type=checkbox]').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var value = this.value;
                if (this.checked) {
                    commodityLayers[value].addTo(map);
                } else {
                    map.removeLayer(commodityLayers[value]);
                }
            });
        });
    </script>
</body>
</html>
