<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;

class UploadTravelCalculatorSheet extends Component implements HasForms
{
    use InteractsWithForms;
    public $attachment = [ 'file_path' => null ];
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file_path')
                    ->label('Upload Travel Calculator Sheet')
                    ->disk('s3')
                    ->directory('travel-calculator/uploaded-sheets')
                    ->visibility('public')
                    
                // ...
            ])
            ->statePath('attachment');
           
    }
    public function submit()
{
    $form = $this->form->getState();
    // Handle file upload and get the file path
    $filePath = $form['file_path'] ?? null;
    if ($filePath) {
        // Assuming the file is stored in the 'public' directory, adjust the path as necessary
        dd($filePath);
        $storagePath = Storage::path($filePath);
        dd($storagePath);
        
        // Open the file and parse CSV
        if (($handle = fopen($storagePath, 'r')) !== FALSE) {
            // Read the CSV row by row
            fgetcsv($handle); // Skip the header
            $employeeCoordinates = [];
            
            // Iterate through the CSV rows
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $employee_code = $data[0];  // employee_code
                $home_address = $data[1];   // home_address
                $project_address = $data[2]; // project_address
                $office_address = $data[3]; // office_address
                // Get coordinates for the home address using the separate function
                $home_coordinates = $this->getCoordinatesFromAddress($home_address);
                $project_coordinates = $this->getCoordinatesFromAddress($project_address);
                $office_coordinates = $this->getCoordinatesFromAddress($office_address);
                // If coordinates are found, store them in the employeeCoordinates array
                if ($home_coordinates && $project_coordinates && $office_coordinates) {

                    $home_distance = $this->calculateDistance(
                        $home_coordinates['lat'], $home_coordinates['lng'], 
                        $project_coordinates['lat'], $project_coordinates['lng']
                    );
                    $office_distance = $this->calculateDistance(
                        $office_coordinates['lat'], $office_coordinates['lng'], 
                        $project_coordinates['lat'], $project_coordinates['lng']
                    );
                    if ($home_distance < 50 || $office_distance < 50) {
                        $zone= 'Zone 1';
                    }
                    elseif (($home_distance > 50 && $home_distance <= 100) || ($office_distance > 50 && $office_distance <= 100))
                    {
                        $zone= 'Zone 2';
                    }
                    else {
                        $zone= 'Zone 3';
                    }

                    $employeeCoordinates[$employee_code] = [
                        'home' => $home_coordinates,
                        'project' => $project_coordinates,
                        'office' => $office_coordinates,
                        'home_to_project_distance' => $home_distance, // Distance between home and project
                        'office_to_project_distance' => $office_distance, // Distance between home and project
                        'zone' => $zone,

                    ];
                }
            }
            fclose($handle);
            $this->dispatch('childDataSent', $employeeCoordinates);
            // Output the array of employee coordinates
            // return $this->createCsvFromCoordinatesAndDownload($employeeCoordinates);
            // dd($employeeCoordinates);
        } else {
            dd('Failed to open the file');
        }
    } else {
        dd('No file uploaded');
    }
}

    public function getCoordinatesFromAddress(string $address)
{
    // Make a GET request to the Google Maps Geocoding API
    $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
        'address' => $address,
        'key' => env('GOOGLE_PLACES_API_KEY')  // Ensure you have the API key in your .env file
    ]);

    // Check if the request was successful
    if ($response->successful()) {
        // Get the coordinates from the response
        $data = $response->json();
        if (isset($data['results'][0]['geometry']['location'])) {
            $coordinates = $data['results'][0]['geometry']['location'];
            return [
                'lat' => $coordinates['lat'],
                'lng' => $coordinates['lng'],
            ];
        }
    }

    // Return null if no coordinates are found or if the API call fails
    return null;
}

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    // Radius of Earth in kilometers
    $earthRadius = 6371;

    // Convert degrees to radians
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // Haversine formula
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;
    $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;

    return $distance;
}

public function createCsvFromCoordinatesAndDownload($employeeCoordinates)
{
    // Define the file path to save the CSV
    $filePath = storage_path('app/public/employee_coordinates.csv'); // Adjust the path as needed

    // Open a file handle for writing (create the file if it doesn't exist)
    $file = fopen($filePath, 'w');

    // Write the headers for the CSV
    fputcsv($file, [
        'employee_code',
        'home_latitude', 'home_longitude',
        'project_latitude', 'project_longitude',
        'office_latitude', 'office_longitude',
        'home_to_project_distance',
        'office_to_project_distance',
        'zone'
    ]);

    // Loop through the employeeCoordinates array and write each row
    foreach ($employeeCoordinates as $employee_code => $data) {
        // Extract coordinates for home, project, and office
        $home_latitude = $data['home']['lat'] ?? null;
        $home_longitude = $data['home']['lng'] ?? null;
        $project_latitude = $data['project']['lat'] ?? null;
        $project_longitude = $data['project']['lng'] ?? null;
        $office_latitude = $data['office']['lat'] ?? null;
        $office_longitude = $data['office']['lng'] ?? null;

        // Extract distances and zone
        $home_to_project_distance = $data['home_to_project_distance'] ?? null;
        $office_to_project_distance = $data['office_to_project_distance'] ?? null;
        $zone = $data['zone'] ?? null;

        // Write the employee's data to the CSV file
        fputcsv($file, [
            $employee_code,
            $home_latitude, $home_longitude,
            $project_latitude, $project_longitude,
            $office_latitude, $office_longitude,
            $home_to_project_distance,
            $office_to_project_distance,
            $zone
        ]);
    }

    // Close the file handle
    fclose($file);

    // Serve the file to the user for download
    return response()->download($filePath)->deleteFileAfterSend(true);
}
    public function render()
    {
        return view('livewire.upload-travel-calculator-sheet');
    }
}
