<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RequisitionComment;
use Filament\Notifications\Notification;

use Filament\Notifications\Actions\Action;

class CommentList extends Component
{
    public $record; // Declare the public property
    public $commentId; // To store the ID of the comment being edited
    public $newCommentContent; // To store the content of the new or edited comment
    public $selectedCommentId;
    public $commentIdToDelete; 
    protected $listeners = [
        'doDeleteComment', // Listens for the 'doDeleteComment' event
        'commentAdded' => 'refreshComments', // Listens for 'commentAdded' and maps it to 'refreshComments' method
    ];
    

    public function mount($record) // Use mount to initialize the property
    {
        $this->record = $record; // Assign the passed record to the property
    }

    public function editComment($id)
    {
        // Find the comment by ID and set the content for editing
        $comment = $this->record->comments->find($id);
        $this->commentId = $id;
        $this->newCommentContent = $comment->content; // Pre-fill the content for editing
    }

    public function updateComment()
    {
        // Validate the new comment content
        $this->validate([
            'newCommentContent' => 'required|string|max:500',
        ]);

        // Find the comment and update its content
        $comment = $this->record->comments->find($this->commentId);
        $comment->content = $this->newCommentContent;
        $comment->save();

        // Reset the editing state
        $this->reset(['commentId', 'newCommentContent']);

        Notification::make()
        ->success()
        ->body('Updated successfully')->send();
    }

    public function deleteComment($id)
    {
        // Store the ID of the comment to delete
        
        $this->commentIdToDelete = $id;
    
        Notification::make($id)
            ->warning()
            ->title(__('Delete confirmation'))
            ->body(__('Are you sure you want to delete this comment?'))
            ->actions([
                Action::make('confirm')
                    ->label(__('Confirm'))
                    ->color('danger')
                    ->button()
                    ->close()
                    ->dispatch('doDeleteComment', ['commentId' => $this->commentIdToDelete]), // Pass the comment ID to delete
                Action::make('cancel')
                    ->label(__('Cancel'))
                    ->close()
            ])
            ->persistent()
            ->send();
    }
    public function doDeleteComment($commentId): void
{
    // dd($commentId);
    // Find the comment and delete id
    $comment = RequisitionComment::find($commentId);

    if ($comment) {
        $comment->delete();
        // Refresh the record to get the latest comments
        Notification::make()
        ->success() // Use success instead of warning since you're notifying about a successful delete
        ->title(__('Comment deleted'))
        ->body(__('The comment has been successfully deleted.'))
        ->send();
       
    } else {
        $this->notify('error', __('Comment not found'));
    }
}
public function refreshComments()
    {
        // This method can be used to refresh the comments
        // You can simply call render to refresh the view or fetch the comments again if needed
        $this->render();
    }
   
    public function render()
    {
        $comments = RequisitionComment::where('requisition_id', $this->record->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.comment-list'); // Pass the comments to the view
    }
}
