<?php

namespace App\Http\Controllers;

use App\Models\ItemBase;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use League\CommonMark\CommonMarkConverter;
use function Spatie\LaravelPdf\Support\pdf;

class RequisitionPDFController extends Controller
{
    public function __invoke(Requisition $requisition)
    {
        // Retrieve line items for the requisition
        $lineItems = $requisition->lineItems;
        $item_base_id = ItemBase::where('effective_from', '<=', now()->today())->first();
        if ($item_base_id) {
            foreach ($lineItems as &$lineItem) { // Use reference to update the original array
                // Assuming each line item has a 'code' field
                $basePrice = \App\Models\ItemBasePrice::where('material_item_code', $lineItem['item_code'])->where('item_base_id', $item_base_id->id)->first();
               
                if ($basePrice) {
                    $lineItem['cost'] = $basePrice->price; // Update price if found
                }
                else $lineItem['cost'] = 0;
            }
        }
       
        // dd($lineItems);
        $converter = new CommonMarkConverter();
        $notesHtml = $requisition->notes ? $converter->convert($requisition->notes) : ''; //Convert only if notes is not null
        $pdf = Pdf::loadView('pdf.invoice', [
            'date_required' => $requisition->date_required,
            'supplier' => $requisition->supplier_name, // or use a relevant field
            'project_id' => $requisition->projectsetting->name,
            'site_ref' => $requisition->site_reference,
            'delivery_contact' => $requisition->delivery_contact,
            'pickup_by' => $requisition->pickup_by,
            'requested_by' => $requisition->requested_by,
            'delivery_to' => $requisition->deliver_to,
            'notes' => $notesHtml,
            'req_lines' => $lineItems, // Pass line items to the view
        ]);

        // Return the generated PDF as a response
        return $pdf->download("{$requisition->requisition_number}.pdf", 'requisition.pdf');
    }
}
