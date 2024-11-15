<x-filament-panels::page>
    <!-- Render the Livewire component -->
    @livewire('UploadTravelCalculatorSheet')

    <!-- Display employeeCoordinates if available -->
    @if($employeeCoordinates)
        <h3>Employee Coordinates</h3>
        {{-- <pre>{{ print_r($employeeCoordinates, true) }}</pre> --}}
        
        <div class="mb-4">
            {{-- <button wire:click="downloadCSV" class="px-4 py-2 bg-blue-500 text-white rounded-md">
                Download CSV
            </button> --}}
            <x-filament::button  wire:click="downloadCSV" icon="heroicon-m-sparkles">
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
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $employee }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $coordinates['home_to_project_distance'] }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $coordinates['office_to_project_distance'] }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $coordinates['zone'] }}
                    </td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

    @else
        <p>Upload sheet to get results.</p>
        
    @endif
</x-filament-panels::page>