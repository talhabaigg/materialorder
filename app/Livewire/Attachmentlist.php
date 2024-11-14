<?php

namespace App\Livewire;

use Livewire\Component;

class Attachmentlist extends Component
{
    public $record; // This will store the passed record
    
    // Accept the record via the mount method
    public function mount($record)
    {
        $this->record = $record; // Initialize the record property
    }

    public function render()
    {
        // Get the attachments for the given record
        $attachments = $this->record->attachments->sortByDesc('created_at'); // Assuming 'attachments' relationship

        return view('livewire.attachmentlist', compact('attachments')); // Pass the attachments to the view
    }
}
