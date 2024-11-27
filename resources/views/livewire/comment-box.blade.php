<div>
    <form wire:submit.prevent="submitComment" class="space-y-2">
        <!-- Bind comment input to Livewire property -->
        <x-filament::input.wrapper>
            <x-filament::input type="text" wire:model.defer="comment" wire:keydown.enter="submitComment" />
        </x-filament::input.wrapper>
        <x-filament::button wire:click="submitComment">
            Add Comment
        </x-filament::button>
    </form>
</div>
