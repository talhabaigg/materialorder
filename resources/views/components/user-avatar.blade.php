<div>
    @php($user = $getRecord())

    {{-- Check if the user record exists --}}
    @if($user)
        @php($uniqid = uniqid())  <!-- Generate a unique ID for popover -->
        <div class="relative inline-block">
            <!-- User Avatar -->
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->creator->name) }}&background=2563eb&color=fff&size=128"
                 alt="{{ $user->name }}"
                 data-popover-target="popover-user-{{ $user->id }}-{{ $uniqid }}"
                 class="w-6 h-6 rounded-full bg-gray-200 bg-cover bg-center" />

            <!-- Tooltip Content -->
            <div data-popover id="popover-user-{{ $user->id }}-{{ $uniqid }}" role="tooltip"
                 class="absolute z-10 w-64 text-sm font-light text-gray-500
                        bg-white rounded-lg border border-gray-200 shadow-sm opacity-0
                        transition-opacity duration-300 invisible dark:text-gray-400 dark:bg-gray-800
                        dark:border-gray-600">
                <div class="p-3">
                    <div class="flex justify-between items-center mb-2">
                        <img class="w-10 h-10 rounded-full"
                             src="https://ui-avatars.com/api/?name={{ urlencode($user->creator->name) }}&background=2563eb&color=fff&size=128" alt="{{ $user->name }}">
                    </div>
                    
                </div>
                <div data-popper-arrow></div>
            </div>
        </div>
    @else
        <!-- Optional: Add a message if no user is found -->
        <p class="text-gray-500">{{ __('User not found') }}</p>
    @endif
</div>
