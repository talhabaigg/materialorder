<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\RequisitionLineItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequisitionController extends Controller
{
   
    public function uploadCsv(Request $request, Requisition $requisition)
    {
        // Validate the CSV file path
        dd($requisition);
        $request->validate(['csv' => 'required|string']);
    
        // Get the full path of the CSV file
        $filePath = $request->input('csv'); // This should contain the path
        $fullFilePath = storage_path('app/public/' . $filePath); // Construct full path
    
        // Check if the file exists
        if (!file_exists($fullFilePath)) {
            return redirect()->back()->withErrors(['csv' => 'The specified CSV file does not exist.']);
        }
    
        // Process the CSV file
        $rows = array_map('str_getcsv', file($fullFilePath));
        array_shift($rows); // Skip the header row
        dd($rows);
        // Prepare items for update
        $itemsToUpdate = [];
        foreach ($rows as $row) {
            if (count($row) < 4) continue; // Ensure enough columns
            $itemsToUpdate[] = [
                'item_code' => $row[0],
                'description' => $row[1],
                'qty' => (int)$row[2],
                'cost' => isset($row[3]) && is_numeric($row[3]) ? (float)$row[3] : 0, // Default to 0 if cost not provided
            ];
        }
    
        // Update requisition items
        $requisition->lineItems()->detach(); // Remove existing items
        foreach ($itemsToUpdate as $itemData) {
            $item = RequisitionLineItem::updateOrCreate(
                ['item_code' => $itemData['item_code']],
                [
                    'description' => $itemData['description'],
                    'qty' => $itemData['qty'],
                    'cost' => $itemData['cost']
                ]
            );
            $requisition->lineItems()->attach($item->id); // Attach new/updated item
        }
    
        return redirect()->back()->with('success', 'CSV file processed and requisition updated successfully.');
    }
}