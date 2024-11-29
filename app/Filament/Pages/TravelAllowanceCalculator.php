<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TravelAllowanceCalculator extends Page
{
    use HasPageShield;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    public $employeeCoordinates = [];
    protected static ?string $navigationGroup = 'Admin';
    // Listen for the event emitted by the child component
    protected $listeners = ['childDataSent' => 'handleChildData'];

    // Handle the data sent from the child
    public function handleChildData($data)
    {
        // dd($data);
        // Store the received data in the employeeCoordinates property
        $this->employeeCoordinates = $data;
    }
    public function downloadCSV()
    {
        $data = $this->employeeCoordinates;

        // Create a response that will trigger the CSV download
        $response = new StreamedResponse(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // Add the headers to the CSV file
            fputcsv($handle, [
                'Employee',
                'Home',
                'Project',
                'Home to Project Distance',
                'Office to Project Distance',
                'Zone'
            ]);

            // Add the rows
            foreach ($data as $employee => $coordinates) {
                fputcsv($handle, [
                    $employee,
                    $coordinates['home']['lat'] . ',' . $coordinates['home']['lng'],
                    $coordinates['project']['lat'] . ',' . $coordinates['project']['lng'],
                    $coordinates['home_to_project_distance'],
                    $coordinates['office_to_project_distance'],
                    $coordinates['zone'],
                ]);
            }

            fclose($handle);
        });

        // Set headers for CSV download
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="employee_coordinates.csv"');

        return $response;
    }
    protected static string $view = 'filament.pages.travel-allowance-calculator';


}
