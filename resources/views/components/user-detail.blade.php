@php($user = $record->creator)
@if($user)
<div class="rounded-lg max-w-xs flex flex-col items-center">
    <div class="mb-2 flex justify-center w-full">
        <img 
            class="w-8 h-8 rounded-full"
            src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563eb&color=fff&size=128" 
            alt="{{ $user->name }}">
    </div>
</div>

@endif

