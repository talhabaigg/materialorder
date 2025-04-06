<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\ItemBase;
use App\Models\Requisition;
use Illuminate\Support\Str;
use App\Models\MaterialItem;
use Illuminate\Http\Request;
use App\Models\ItemBasePrice;
use App\Models\ItemProjectPrice;
use App\Models\RequisitionComment;
use App\Models\RequisitionLineItem;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
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
            if (count($row) < 4)
                continue; // Ensure enough columns
            $itemsToUpdate[] = [
                'item_code' => $row[0],
                'description' => $row[1],
                'qty' => (int) $row[2],
                'cost' => isset($row[3]) && is_numeric($row[3]) ? (float) $row[3] : 0, // Default to 0 if cost not provided
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

    // This generates an import file to use with Premier Construction Software
    public function excelImport(Requisition $requisition)
    {
        // Generate a unique file name with a UUID
        $uuid = Str::uuid();

        $fileName = sprintf('PO-%s-import.xlsx', $uuid);

        // Define the file path
        $filePath = storage_path('app/public/' . $fileName);

        // Create and export an Excel file using a closure
        Excel::store(new class ($requisition) implements \Maatwebsite\Excel\Concerns\FromCollection {
            protected $requisition;

            public function __construct($requisition)
            {
                $this->requisition = $requisition;
            }

            public function collection()
            {
                $requisition = Requisition::with('lineItems')->find($this->requisition->id);

                // Prepare the header row
                $headers = [
                    'AP Subledger',
                    'PO #',
                    'Vendor Code',
                    'Job #',
                    'Memo',
                    'PO Date',
                    'Required Date',
                    'Promised Date',
                    'Ship To Type',
                    'Ship To',
                    'Requested By',
                    'Line',
                    'Item Code',
                    'Line Description',
                    'Qty',
                    'UofM',
                    'Unit Cost',
                    'Distribution Type',
                    'Line Job #',
                    'Cost Item',
                    'Cost Type',
                    'Department',
                    'Location',
                    'GL Account',
                    'GL Division',
                    'GL SubAccount',
                    'Tax Group',
                    'Discount %'
                ];

                // Initialize the collection with the header row
                $rows = collect([$headers]);

                // Iterate over line items and prepare each row
                foreach ($requisition->lineItems as $index => $lineItem) {

                    $materialItem = MaterialItem::where('code', $lineItem->item_code)->first();
                    $costcode = $materialItem?->costcode;

                    // Format only if costcode is present
                    $formattedCostcode = $costcode
                        ? substr($costcode, 0, 2) . '-' . substr($costcode, 2)
                        : 'N/A'; // or '' or null depending on what you want in export

                    $row = [
                        'AP Subledger' => 'AP',
                        'PO #' => 'NEXT #',
                        'Vendor Code' => $requisition->supplier_name,
                        'Job #' => $requisition->site_reference,
                        'Memo' => $requisition->notes,
                        'PO Date' => now()->toDateString(),
                        'Required Date' => $requisition->date_required,
                        'Promised Date' => $requisition->date_required,
                        'Ship To Type' => 'JOB',
                        'Ship To' => $requisition->site_reference,
                        'Requested By' => $requisition->requested_by,
                        'Line' => $index + 1,
                        'Item Code' => '',
                        'Line Description' => $lineItem->item_code . '-' . $lineItem->description,
                        'Qty' => $lineItem->qty,
                        'UofM' => 'EA',
                        'Unit Cost' => $lineItem->cost,
                        'Distribution Type' => 'J',
                        'Line Job #' => $requisition->site_reference,
                        'Cost Item' => $formattedCostcode,
                        'Cost Type' => 'MAT',
                        'Department' => '',
                        'Location' => '',
                        'GL Account' => '',
                        'GL Division' => '',
                        'GL SubAccount' => '',
                        'Tax Group' => 'GST',
                        'Discount %' => ''
                    ];

                    $rows->push($row);
                }

                return $rows;
            }
        }, $fileName, 'public', ExcelFormat::XLSX);

        // Return the file as a download and delete it after sending
        return response()->download($filePath, $fileName)->deleteFileAfterSend();
    }



    //Sage import is not relevant to the current project anymore. It has been removed.
    // public function sageImport(Requisition $requisition)
    // {
    //     // Define the file path and name
    //     $filePath = storage_path('app/public/sage_import.txt');
    //     $file = fopen($filePath, 'w');
    //     $poDate = now()->format('d/m/Y');
    //     $currentDate = now();
    //     $itemBase = ItemBase::whereDate('effective_from', '<=', $currentDate)
    //         ->whereDate('effective_to', '>=', $currentDate)->orWhereNull('effective_to')
    //         ->first();
    //     // Prepare CSV headers
    //     $headers = [
    //         'C',
    //         '',
    //         '',
    //         '',
    //         // 'ALLF00', //VENDOR CODE
    //         $requisition->supplier_name,
    //         // '22/11/2024', //PO DATE
    //         $poDate,
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         '',
    //         $formattedDate = Carbon::parse($requisition->date_required)->format('d/m/Y'),

    //     ];
    //     fputcsv($file, $headers);
    //     foreach ($requisition->lineItems as $item) {
    //         $materialItem = MaterialItem::where('code', $item->item_code)->first();
    //         $costcode = $materialItem ? $materialItem->costcode : '';
    //         if (strlen($costcode) == 5) {
    //             // Split the costcode into two parts: first 2 digits and the last 3 digits
    //             $costcode = substr($costcode, 0, 2) . '-' . substr($costcode, 2);
    //         }


    //         $projectprice = ItemProjectPrice::where('item_code', $item->item_code)->where('project_number', $requisition->site_reference)->first()?->price;
    //         if ($projectprice) {
    //             $cost = $projectprice;
    //         } else
    //             $cost = ItemBasePrice::where('material_item_code', $item->item_code)->where('item_base_id', $itemBase ? $itemBase->id : 0)->first()?->price;

    //         $description = str_replace('"', '""', $item->description);
    //         $data = [
    //             'CI',              // Type
    //             '',                // Field1
    //             '',                // Field2
    //             $description ?? '',  // Description
    //             '',
    //             $poDate,           // PO Date (repeats for each line)
    //             '',
    //             $requisition->site_reference,      // Code1
    //             '',                // Field3
    //             $costcode,          // Code2
    //             'M',               // Unit Type
    //             'GST',             // Tax Type
    //             '',                // Field4
    //             $item->qty ?? '0',    // Quantity (unit qty)
    //             $cost, // Unit cost per item
    //             '',                // Field5
    //             '',                // Field6
    //             '',                // Field7
    //             '',                // Field8
    //             '',                // Field9
    //             $item->item_code,      // Reference
    //             '',                // Field10
    //             '',                // Field11
    //             '',                // Field12
    //             '',                // Field13
    //             '',                // Field14
    //         ];
    //         // Write each row to the file without unnecessary quotes
    //         fputcsv($file, $data, ',', '"');  // Ensure no quotes are added // Ensure no quotes are added
    //         // The fourth argument controls the enclosure (use empty quotes for no enclosure)
    //     }




    //     // Close the file
    //     fclose($file);

    //     return response()->download($filePath, $requisition->requisition_number . '-sage_import_.txt')->deleteFileAfterSend();

    // }
}