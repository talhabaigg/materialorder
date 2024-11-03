<?php

use App\Livewire\Home;
use App\Models\Requisition;
use App\Livewire\Post\Show as PostShow;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\RequisitionPDFController;

Route::get('/', function () {
    return redirect('/admin/login');
})->name('home');
// Route::get('/article/{post:slug}', PostShow::class)->name('post.show');
Route::get('requisition/pdf/{requisition}', RequisitionPDFController::class)->name('requisition.pdf');
Route::post('/requisitions/{requisition}/upload-csv', [RequisitionController::class, 'uploadCsv'])
    ->name('requisition.uploadCsv');

    Route::get('/requisitions/share/{requisition}', function (Requisition $requisition) {
        return redirect()->to(route('filament.resources.requisitions.view', $requisition));
    })->name('filament.resources.requisitions.share');
    
    Route::post('/requisitions/{id}/submit-comment', [RequisitionController::class, 'submitComment'])
    ->name('requisitions.submitComment');