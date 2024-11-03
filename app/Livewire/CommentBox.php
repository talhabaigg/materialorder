<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RequisitionComment;
use Filament\Notifications\Notification;

class CommentBox extends Component
{

    public $record; // Holds the record passed from the parent
    public $comment = ''; // Holds the comment text
    public function mount($record) // Use mount to initialize the property
    {
        $this->record = $record; // Assign the passed record to the property
    }
    

    

    public function submitComment()
    {
        // Validate the comment input
        $this->validate([
            'comment' => 'required|string|max:1000', // Adjust validation rules as needed
        ]);

        // Create a new comment
        RequisitionComment::create([
            'content' => $this->comment,
            'user_id' => auth()->id(), // Assuming you want to associate the comment with the authenticated user
            'requisition_id' => $this->record->id, // Adjust as necessary based on your database schema
        ]);

        // Reset the comment input
        $this->reset('comment');
        
        Notification::make()
        ->success()
        ->body('Added comment successfully')->send();
        $this->dispatch('commentAdded');
    }

    public function render()
    {
        return view('livewire.comment-box'); // Pass the comments to the view
    }
}
