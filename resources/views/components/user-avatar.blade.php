@php($user = $getRecord()->creator)
{{-- @dd($user); --}}
@vite(['resources/js/app.js'])


@if($user)
    <div>
        @php($uniqid = uniqid())
        
        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563eb&color=fff&size=128"
             alt="{{ $user->name }}"
             data-popover-target="popover-user-{{ $user->id }}-{{ $uniqid }}"
             class="w-6 h-6 rounded-full  bg-cover bg-center"/>

        <div data-popover id="popover-user-{{ $user->id }}-{{ $uniqid }}" role="tooltip"
             class="inline-block absolute invisible w-64 text-sm font-light text-gray-500
                                        bg-white rounded-lg border border-gray-200 shadow-sm opacity-0
                                        transition-opacity duration-300 dark:text-gray-400 dark:bg-gray-800
                                        dark:border-gray-600" style="z-index: 9999;">
            <div class="p-3">
                <div class="flex justify-between items-center mb-2">
                    <img class="w-10 h-10 rounded-full"
                    src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563eb&color=fff&size=128" alt="{{ $user->name }}">
                </div>
                <p class="text-base font-semibold leading-none text-gray-900 dark:text-white">
                    <a>{{ $user->name }}</a>
                </p>
                <p class="mb-3 text-sm font-normal">
                    <a href="mailto:{{ $user->email }}"
                       class="hover:underline">
                        {{ $user->email }}
                    </a>
                </p>
                <p class="mb-4 text-sm font-light">
                    {{ __('Member since') }}
                    <a class="text-blue-600 dark:text-blue-500">
                        {{ $user->created_at->format('Y-m-d') }}
                    </a>
                </p>
                
               
            </div>
            <div data-popper-arrow></div>
        </div>
    </div>
@endif


