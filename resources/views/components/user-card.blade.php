@php
    // Check if creator exists, if not, use $record as the user
    $user = $record->creator ?? $record;
    // dd($user);
@endphp
@if($user)
<div class="bg-white dark:bg-gray-800 rounded-lg p-4 max-w-xs shadow-md">
    <div class="flex justify-between items-center mb-2">
        <img 
            class="w-10 h-10 rounded-full"
            src={{$user->getAvatarUrl()}}
            alt="{{ $user->name }}">
    </div>
    <p class="text-base font-semibold leading-none text-gray-900 dark:text-white">
        <a>{{ $user->name }}</a>
    </p>
    <p class="mb-3 text-sm font-normal text-gray-600 dark:text-gray-300">
        <a href="mailto:{{ $user->email }}" class="hover:underline text-blue-600 dark:text-blue-400">
            {{ $user->email }}
        </a>
    </p>
    <p class="mb-4 text-sm font-light text-gray-500 dark:text-gray-400">
        {{ __('Member since') }}
        <a class="text-blue-600 dark:text-blue-400">
            {{ $user->created_at->format('d-m-Y') }}
        </a>
    </p>
</div>

@endif

