<div>
   <form wire:submit.prevent="submitComment" class="pb-5">
    <!-- Bind comment input to Livewire property -->
    <textarea wire:model.defer="comment" placeholder="Type a new comment" class="w-full p-2 border rounded-xl bg-gray-100 text-gray-700"  wire:keydown.enter="submitComment"   required></textarea>
    <button wire:click="submitComment" class="px-3 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded mt-3">Add Comment</button>
</form>


</div>
