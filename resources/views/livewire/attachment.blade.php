<div>
    <form wire:submit.prevent="submitAttachment" class="pb-5">
        {{ $this->form }}
        <x-filament::button size="sm" class="mt-2" wire:click="submitAttachment">
            Upload
        </x-filament::button>
    </form>
</div>
