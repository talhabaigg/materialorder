<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;
use function Spatie\LaravelPdf\Support\pdf;

class RequisitionPDFController extends Controller
{
    public function __invoke(Requisition $requisition)
    {
        // Retrieve line items for the requisition
        $lineItems = $requisition->lineItems;

        return pdf('pdf.invoice', [
            'date_required' => $requisition->date_required,
            'supplier' => $requisition->supplier_id, // or use a relevant field
            'project_id' => $requisition->project_id,
            
            'site_ref' => $requisition->site_reference,
            'delivery_contact' => $requisition->delivery_contact,
            'pickup_by' => $requisition->pickup_by,
            'requested_by' => $requisition->requested_by,
            'delivery_to' => $requisition->deliver_to,
            'notes' => $requisition->notes,

            'req_lines' => $lineItems, // Pass line items to the view
            // Pass other necessary data
        ]);
    }
}
