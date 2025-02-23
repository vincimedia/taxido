@use('Nwidart\Modules\Facades\Module')
@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('modules/taxido/css/vendors/leaflet/leaflet.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('modules/taxido/js/leaflet.min.js') }}" defer></script>
    <script>
        (function($) {
            "use strict";

            $(document).ready(function() {
                let map;
                let markers = [];
                const locations = @json($locations);
                const defaultImage = '{{ asset('images/user.png') }}';
                let vehicleTypesFilter = [];
                let zoneFilter = ''; 

                function initialize() {
                    setupMap();
                    addMarkers(locations);
                }

                function setupMap() {
                    map = L.map('map_canvas').setView([21.20764938296402, 72.77381805168456], 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                }

                function getVehicleIcon(vehicleImage) {
                    return L.icon({
                        iconUrl: vehicleImage || defaultImage,
                        iconSize: [50, 32],
                    });
                }

                function addMarkers(locations) {
                    markers.forEach(marker => map.removeLayer(marker));
                    markers = [];

                    locations.forEach(location => {
                        // Apply vehicle type filter
                        if (vehicleTypesFilter.length > 0 && !vehicleTypesFilter.includes(location
                                .vehicle_type)) {
                            return;
                        }

                        // Apply zone filter
                        if (zoneFilter && location.zone_id !== zoneFilter) {
                            return;
                        }

                        if (location.lat && location.lng) {
                            let marker = L.marker([location.lat, location.lng], {
                                icon: getVehicleIcon(location.vehicle_image)
                            }).addTo(map);

                            marker.vehicleType = location.vehicle_type;
                            marker.zoneId = location.zone_id; // Store zone ID in marker

                            const contentString = `
                            <div class="driver-location-box">
                                <div class="vehicle-image">
                                    <img src="${location.image || defaultImage}" class="img-fluid" />
                                </div>
                                <h5><span>${location.name}</span></h5>
                                <ul class="location-list">
                                    <li class="rate-box">Rating: <span><i class="ri-star-fill"></i> ${location.rating || 'Unrated'}</span></li>
                                    <li>Vehicle: <span>${location.vehicle_name}</span></li>
                                    <li>Phone: <span>${location.phone}</span></li>
                                    <li>Model: <span>${location.vehicle_model}</span></li>
                                    <li>Plate Number: <span>${location.plate_number}</span></li>
                                </ul>
                            </div>
                        `;

                            marker.bindPopup(contentString);
                            markers.push(marker);
                        }
                    });
                }

                $('.vehicle-filter').on('change', function() {
                    vehicleTypesFilter = [];
                    $('.vehicle-filter:checked').each(function() {
                        vehicleTypesFilter.push($(this).val());
                    });

                    addMarkers(locations);
                    filterDriverList();
                    updateMapWithFilters();
                });

                function updateMapWithFilters() {
                    markers.forEach(function(marker) {
                        const showMarker = (vehicleTypesFilter.length === 0 || vehicleTypesFilter
                                .includes(marker.vehicleType)) &&
                            (zoneFilter === '' || marker.zoneId === zoneFilter);
                        if (showMarker) {
                            marker.setOpacity(1);
                            marker.setVisible(true);
                        } else {
                            marker.setOpacity(0);
                            marker.setVisible(false);
                        }
                    });
                }

                function filterDriverList() {
                    const selectedVehicleTypes = vehicleTypesFilter;
                    const drivers = document.querySelectorAll('.accordion-item');
                    drivers.forEach(function(driverItem) {
                        const driverVehicleType = driverItem.getAttribute('data-vehicle-type');
                        const driverZoneId = driverItem.getAttribute('data-zone-id');

                        if (selectedVehicleTypes.length > 0 && !selectedVehicleTypes.some(function(
                                type) {
                                return driverVehicleType.includes(type.toLowerCase());
                            })) {
                            driverItem.style.display = "none";
                        } else if (zoneFilter && driverZoneId !== zoneFilter) {
                            driverItem.style.display = "none";
                        } else {
                            driverItem.style.display = "block";
                        }
                    });
                }

                $('#zone_id').on('change', function() {
                    zoneFilter = $(this).val();
                    addMarkers(locations);
                    filterDriverList();
                    updateMapWithFilters();
                });

                $('button[data-driver-id]').on('click', function() {
                    const driverId = $(this).data('driver-id');
                    const driver = locations.find(d => d.id === driverId);

                    if (driver && driver.lat && driver.lng) {
                        const position = [driver.lat, driver.lng];
                        map.setView(position, map.getZoom());

                        let marker = L.marker(position, {
                            icon: getVehicleIcon(driver.vehicle_image)
                        }).addTo(map);

                        const contentString = `
                        <div class="driver-location-box">
                            <div class="vehicle-image">
                                <img src="${driver.image || defaultImage}" class="img-fluid" />
                            </div>
                            <h5><span>${driver.name}</span></h5>
                            <ul class="location-list">
                                <li class="rate-box">Rating : <span><i class="ri-star-fill"></i> 4.0</span></li>
                                <li>Vehicle: <span>${driver.vehicle_name}</span></li>
                                <li>Phone: <span>${driver.phone}</span></li>
                                <li>Vehicle Model: <span>${driver.vehicle_model}</span></li>
                                <li>Plate Number: <span>${driver.plate_number}</span></li>
                            </ul>
                        </div>
                    `;
                        marker.bindPopup(contentString);
                        markers.push(marker);
                    } else {
                        alert('Unable to retrieve driver location.');
                    }
                });

                initialize();
            });
        })(jQuery);
    </script>
@endpush
