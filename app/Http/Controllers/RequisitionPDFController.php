<?php

namespace App\Http\Controllers;

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
        $converter = new CommonMarkConverter();
        $notesHtml = $converter->convertToHtml($requisition->notes);
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
