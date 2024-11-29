<x-filament-panels::page>
    <!-- Render the Livewire component -->
    @livewire('UploadTravelCalculatorSheet')

    @livewire('map-component', ['coordinates' => $employeeCoordinates])

    <!-- Display employeeCoordinates if available -->
    @if ($employeeCoordinates)

        <h3>Employee Coordinates</h3>
        {{-- <pre>{{ print_r($employeeCoordinates, true) }}</pre> --}}

        <div class="mb-4">
            {{-- <button wire:click="downloadCSV" class="px-4 py-2 bg-blue-500 text-white rounded-md">
                Download CSV
            </button> --}}
            <x-filament::button wire:click="downloadCSV" icon="heroicon-m-sparkles">
                Download using AI
            </x-filament::button>
        </div>


        <div class="relative overflow-x-auto rounded-lg shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Employee
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Home to Project Distance
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Office to Project Distance
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Zone
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($employeeCoordinates as $employee => $coordinates)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $employee }}
                            </th>
                            {{-- <td class="px-6 py-4">
                                {{ $coordinates['home'] }} Km
                            </td> --}}
                            <td class="px-6 py-4">
                                {{ number_format($coordinates['home_to_project_distance'], 2) }} Km
                            </td>
                            <td class="px-6 py-4">
                                {{ number_format($coordinates['office_to_project_distance'], 2) }} Km
                            </td>
                            <td class="px-6 py-4">
                                <x-filament::badge>
                                    {{ $coordinates['zone'] }}
                                </x-filament::badge>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    @else
        <p>Upload sheet to get results.</p>

    @endif
    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <!-- Leaflet Draw CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

        <!-- Leaflet Draw JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof L !== 'undefined') {
                    // Initialize the map
                    const map = L.map('map').setView([-28.0167, 153.4000], 9);
                    // const drawnItems = new L.FeatureGroup().addTo(map);
                    // const pointA = [-26.7769664, 153.1299432];
                    // const pointB = [-28.0319392, 153.4278018];

                    // Create a polyline (line) between the two points
                    // const line = L.polyline([pointA, pointB], {
                    //     color: 'red', // Line color
                    //     weight: 2, // Line thickness
                    //     opacity: 0.7 // Line opacity
                    // }).addTo(map);
                    // Add OpenStreetMap tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors',
                    }).addTo(map);
                    // var imageUrl = 'https://maps.lib.utexas.edu/maps/historical/newark_nj_1922.jpg',
                    //     imageBounds = [
                    //         [40.712216, -74.22655],
                    //         [40.773941, -74.12544]
                    //     ];
                    // L.imageOverlay(imageUrl, imageBounds).addTo(map);
                    // map.fitBounds(imageBounds);
                    L.marker([-28.0010902, 153.3390338]).addTo(map)
                        .bindPopup('Superior Group Office')
                        .openPopup();
                    // L.marker([40.75903300669212, -74.18754799640739]).addTo(map)
                    //     .bindPopup('Superior Group Office')
                    //     .openPopup();

                    // const drawControl = new L.Control.Draw({
                    //     edit: {
                    //         featureGroup: drawnItems,
                    //     },
                    //     draw: {
                    //         polygon: true,
                    //         marker: false, // Disable marker drawing
                    //         circle: false, // Disable circle drawing
                    //         rectangle: false // Disable rectangle drawing
                    //     }
                    // });
                    // map.addControl(drawControl);

                    // Listen for when a polygon is drawn and capture the coordinates
                    // map.on('draw:created', function(event) {
                    //     const layer = event.layer;
                    //     drawnItems.addLayer(layer);

                    //     // You can access the drawn polygon's coordinates like this:
                    //     const coordinates = layer.getLatLngs();
                    //     console.log(coordinates);
                    //     // You can now use these coordinates to process further
                    // });

                    Livewire.on('childDataSent', function(employeeCoordinates) {

                        employeeCoordinates.forEach(coordObj => {
                            Object.entries(coordObj).forEach(([employee, data]) => {
                                console.log('Adding marker for:', employee, data.home.lat, data
                                    .home.lng);

                                if (data.home && data.home.lat && data.home.lng) {
                                    L.marker([data.home.lat, data.home.lng]).addTo(map)
                                        .bindPopup(
                                            `Employee: ${employee}<br>Zone: ${data.zone}`)
                                        .openPopup();
                                }
                            });
                        });
                    });

                } else {
                    console.error('Leaflet is not defined.');
                }
            });
        </script>
    @endpush

</x-filament-panels::page>
