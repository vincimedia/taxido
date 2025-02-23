@push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCWcdJ-T1JZdw9vPEkjHPDBcDNV35MbKXQ"></script>
    
    <script>
        (function($) {
            "use strict";
            let map;
            let markers = [];
            let infoWindow = new google.maps.InfoWindow();
            let locations = @json($locations); 
            const defaultImage = '{{ asset('images/user.png') }}';
            let vehicleTypesFilter = [];
            let zoneFilter = '';

            function initialize() {
                const mapOptions = {
                    zoom: 13,
                    center: new google.maps.LatLng(21.20764938296402, 72.77381805168456),
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    overviewMapControl: true
                };

                map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
                addMarkers(locations);
            }

            function getVehicleIcon(vehicleImage) {
                return {
                    url: vehicleImage || defaultImage,
                    scaledSize: new google.maps.Size(20, 40)
                };
            }

            function addMarkers(locations, filteredVehicleTypes = [], filterZone = '') {
                if (!Array.isArray(locations)) {
                    console.error("Locations array is not valid.");
                    return;  // Exit if locations is not valid
                }

                markers.forEach(marker => marker.setMap(null)); // Clear existing markers
                markers = [];

                locations.forEach(location => {
                    if (filteredVehicleTypes.length > 0 && !filteredVehicleTypes.includes(location.vehicle_type)) {
                        return;
                    }

                    if (filterZone && location.zone_id !== filterZone) {
                        return;
                    }

                    if (location.lat && location.lng) {
                        let marker = new google.maps.Marker({
                            position: new google.maps.LatLng(location.lat, location.lng),
                            map: map,
                            icon: getVehicleIcon(location.vehicle_image),
                            vehicleType: location.vehicle_type,
                            zoneId: location.zone_id
                        });

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
                        marker.addListener('click', function() {
                            infoWindow.setContent(contentString);
                            infoWindow.open(map, marker);
                        });

                        markers.push(marker);
                    }
                });
            }

            $('#refresh-map').on('click', function() {
                refreshMap();
            });

            function refreshMap() {
                $.ajax({
                    url: "{{ route('admin.driver-location.index') }}", 
                    method: 'GET',
                    success: function(data) {
                        if (data.locations && Array.isArray(data.locations)) {
                            locations = data.locations;  // Properly assign new data to locations
                            addMarkers(locations, vehicleTypesFilter, zoneFilter);
                        } else {
                            console.error("Invalid locations data received.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching driver locations: " + error);
                    }
                });
            }

            $(".vehicle-filter").on('change', function() {
                vehicleTypesFilter = [];
                $(".vehicle-filter:checked").each(function() {
                    vehicleTypesFilter.push($(this).val());
                });

                addMarkers(locations, vehicleTypesFilter, zoneFilter);
                filterDriverList();
            });

            $('#zone_id').on('change', function() {
                zoneFilter = $(this).val();
                addMarkers(locations, vehicleTypesFilter, zoneFilter);
                filterDriverList();
            });

            function filterDriverList() {
                const selectedVehicleTypes = vehicleTypesFilter;
                const drivers = document.querySelectorAll('.accordion-item');
                drivers.forEach(function(driverItem) {
                    const driverVehicleType = driverItem.getAttribute('data-vehicle-type');
                    const driverZoneId = driverItem.getAttribute('data-zone-id');

                    if (selectedVehicleTypes.length > 0 && !selectedVehicleTypes.includes(driverVehicleType)) {
                        driverItem.style.display = "none";
                    } else if (zoneFilter && driverZoneId !== zoneFilter) {
                        driverItem.style.display = "none";
                    } else {
                        driverItem.style.display = "block";
                    }
                });
            }

            $('button[data-driver-id]').on('click', function() {
                const driverId = $(this).data('driver-id');
                const driver = locations.find(d => d.id === driverId);

                if (driver && driver.lat && driver.lng) {
                    const position = new google.maps.LatLng(driver.lat, driver.lng);
                    map.panTo(position);
                } else {
                    alert('Unable to retrieve driver location.');
                }
            });

            $(document).ready(function() {
                initialize();

                addMarkers(locations);

                $('button[data-driver-id]').on('click', function() {
                    const driverId = $(this).data('driver-id');
                    const driver = locations.find(d => d.id === driverId);

                    if (driver && driver.lat && driver.lng) {
                        const position = new google.maps.LatLng(driver.lat, driver.lng);
                        map.panTo(position);
                    } else {
                        alert('Unable to retrieve driver location.');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
