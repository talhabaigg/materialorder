@php($user = $getRecord()->creator)
@if($user)
    <div class="relative inline-block">
        @php($uniqid = uniqid())
        
        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563eb&color=fff&size=128"
             alt="{{ $user->name }}"
             data-tippy-content="
                 <div class='flex items-center p-3'>
                     <img class='w-10 h-10 rounded-full mr-2'
                          src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2563eb&color=fff&size=128'
                          alt='{{ $user->name }}'>
                     <div>
                         <p class='text-base font-semibold leading-none text-gray-900 dark:text-white'>
                             {{ $user->name }}
                         </p>
                         <p class='mb-1 text-sm font-normal'>
                             <a href='mailto:{{ $user->email }}' class='hover:underline'>
                                 {{ $user->email }}
                             </a>
                         </p>
                         <p class='mb-4 text-sm font-light'>
                             {{ __('Member since') }}
                             <span class='text-blue-600 dark:text-blue-500'>
                                 {{ $user->created_at->diffForHumans() }} 
                             </span>
                         </p>
                     </div>
                 </div>
             "
             data-tippy-html="true"
             data-tippy-placement="bottom"
             data-tippy-interactive="true"
             class="w-8 h-8 rounded-full cursor-pointer" />
    </div>
@endif
