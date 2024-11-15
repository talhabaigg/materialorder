<div>

    
    <form wire:submit.prevent="submit" class="space-y-2">
        {{ $this->form }}
      
        <x-filament::button wire:click="submit">
           Upload
        </x-filament::button>
    </form>
</div>
