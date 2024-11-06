<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ItemBase;
use App\Models\Requisition;
use App\Models\MaterialItem;
use Illuminate\Http\Request;
use App\Models\ItemBasePrice;
use App\Models\RequisitionComment;
use App\Models\RequisitionLineItem;
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

    public function submitComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        if ($request->input('selectedCommentId')) {
            RequisitionComment::where('id', $request->input('selectedCommentId'))
                ->update([
                    'content' => $request->input('comment')
                ]);
        } else {
            RequisitionComment::create([
                'user_id' => auth()->user()->id,
                'requisition_id' => $id,
                'content' => $request->input('comment')
            ]);
        }

        return redirect()->back()->with('success', __('Comment saved'));
    }

    public function sageImport(Requisition $requisition)
{
    // Define the file path and name
    $filePath = storage_path('app/public/sage_import.txt');
    $file = fopen($filePath, 'w');
    $poDate = now()->format('d/m/Y');
    $currentDate = now();
    $itemBase = ItemBase::whereDate('effective_from', '<=', $currentDate)
->whereDate('effective_to', '>=', $currentDate)->orWhereNull('effective_to')
->first();
    // Prepare CSV headers
    $headers = [
        'C',
        '',
        '',
        '',
        // 'ALLF00', //VENDOR CODE
        $requisition->supplier_name,
        // '22/11/2024', //PO DATE
        $poDate,
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        $formattedDate = Carbon::parse($requisition->date_required)->format('d/m/Y'),
       
    ];
    fputcsv($file, $headers);
    foreach ($requisition->lineItems as $item) {
        $materialItem = MaterialItem::where('code', $item->item_code)->first();
        $costcode = $materialItem ? $materialItem->costcode : '';
        if (strlen($costcode) == 5) {
            // Split the costcode into two parts: first 2 digits and the last 3 digits
            $costcode = substr($costcode, 0, 2) . '-' . substr($costcode, 2);
        }
        $cost = $materialItem ? $materialItem->basic->price : 0;
        $itemBasePrice = ItemBasePrice::where('material_item_code', $item->item_code)->where('item_base_id', $itemBase? $itemBase->id : 0)->first();
        $cost = $itemBasePrice->price;
        $description = str_replace('"', '""', $item->description);
        $data = [
            'CI',              // Type
            '',                // Field1
            '',                // Field2
            $description ?? '',  // Description
            '',
            $poDate,           // PO Date (repeats for each line)
            '',
            $requisition->site_reference,      // Code1
            '',                // Field3
            $costcode,          // Code2
            'M',               // Unit Type
            'GST',             // Tax Type
            '',                // Field4
            $item->qty ?? '0',    // Quantity (unit qty)
            $cost, // Unit cost per item
            '',                // Field5
            '',                // Field6
            '',                // Field7
            '',                // Field8
            '',                // Field9
            $item->item_code,      // Reference
            '',                // Field10
            '',                // Field11
            '',                // Field12
            '',                // Field13
            '',                // Field14
        ];
         // Write each row to the file without unnecessary quotes
         fputcsv($file, $data, ',', '"');  // Ensure no quotes are added // Ensure no quotes are added
         // The fourth argument controls the enclosure (use empty quotes for no enclosure)
    }
   

    // Open the file for writing
   

    // Add the headers as the first row
  

    // Add the data row
   

    // Close the file
    fclose($file);

    return response()->download($filePath)->deleteFileAfterSend();
}
}