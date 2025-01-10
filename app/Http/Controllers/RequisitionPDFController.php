<?php

namespace App\Http\Controllers;

use App\Models\ItemBase;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use League\CommonMark\CommonMarkConverter;
// use Spatie\LaravelPdf\Facades\Pdf;

class RequisitionPDFController extends Controller
{
    public function __invoke(Requisition $requisition)
    {
        $pdf = pdf::loadView('pdf.invoice', [
            'requisition' => Requisition::with('lineItems')->find($requisition->id),
        ]);
        return $pdf->download("{$requisition->requisition_number}.pdf");
    }
    // public function __invoke(Requisition $requisition)
    // {

    //     $converter = new CommonMarkConverter();
    //     $notesHtml = $requisition->notes ? $converter->convert($requisition->notes) : ''; //Convert only if notes is not null

    //     //convert logo to base64

    //     $pdf = pdf::view('pdf.requisition', [
    //         'requisition_id' => $requisition->requisition_number,
    //         'date_required' => $requisition->date_required,
    //         'supplier' => $requisition->supplier_name, // or use a relevant field
    //         'project_id' => $requisition->projectsetting->name,
    //         'site_ref' => $requisition->site_reference,
    //         'delivery_contact' => $requisition->delivery_contact,
    //         'pickup_by' => $requisition->pickup_by,
    //         'requested_by' => $requisition->requested_by,
    //         'delivery_to' => $requisition->deliver_to,
    //         'notes' => $notesHtml,
    //         'req_lines' => $requisition->lineItems, // Pass line items to the view
    //     ])->footerView('pdf.footer', ['requisition_id' => $requisition->requisition_number])->format('a4');
    //     return $pdf->download("{$requisition->requisition_number}.pdf", 'requisition.pdf');
    // }

}
