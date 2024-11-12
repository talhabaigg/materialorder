<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Forms\Form;
use App\Models\RequisitionAttachment;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;


class Attachment extends Component implements HasForms
{
    use InteractsWithForms;
    
    public $record; // Holds the record passed from the parent
    public $attachment = [ 'file_path' => null ];
    public function mount($record) // Use mount to initialize the property
    {
        $this->record = $record; // Assign the passed record to the property
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file_path')
                ->label('Attachment')
                ->disk('s3')
                ->directory('requisitions/attachments')
                ->uploadingMessage('Uploading attachment...')
                ->acceptedFileTypes(['application/pdf', 'image/*'])
                ->minSize(1)
                ->maxSize(10240)
                ->moveFiles()
                ->visibility('publico')
                ->storeFileNamesIn('original_file_name'),
            ])
            ->statePath('attachment');
          
    }
    public function submitAttachment()
{
    // Validate the form before proceeding
   
 
    $form = $this->form->getState();
    // Handle file upload and get the file path
    $filePath = $form['file_path'] ?? null;
    $originalFileName = $form['original_file_name'] ?? null;
    RequisitionAttachment::create([
        'file_path' => $filePath ?? null, // Store the file path in the database
        'original_file_name' => $originalFileName ?? null, // Store the original file name in the database
        'requisition_id' => $this->record->id, // Use the passed record ID
    ]);
        
     // Reset the file input field
    
     // Notify the user
     Notification::make()
         ->success()
         ->body('Attachment created successfully')
         ->send();
 
     $this->dispatch('attachmentAdded');
}
    public function render()
    {
        return view('livewire.attachment');
    }
}
