<?php

use App\Livewire\Home;
use Illuminate\Support\Facades\Route;
use App\Livewire\Post\Show as PostShow;
use App\Http\Controllers\RequisitionPDFController;

Route::get('/', Home::class)->name('home');
Route::get('/article/{post:slug}', PostShow::class)->name('post.show');
Route::get('requisition/pdf/{requisition}', RequisitionPDFController::class)->name('requisition.pdf');