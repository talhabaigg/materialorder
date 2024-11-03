<x-filament::page >
    <a wire:navigate href="{{ \App\Filament\Resources\RequisitionResource::getUrl() }}" class="flex items-center gap-1 text-gray-500 hover:text-gray-700 font-medium text-xs">
        <x-heroicon-o-arrow-left class="w-4 h-4"/> {{ __('Back to requisition page') }}
    </a>
    

    <div class="grid sm:grid-cols-3 gap-5">
        <div class="col-span-1 sm:col-span-2 bg-white dark:bg-gray-900 shadow-sm p-10 rounded-lg">
        
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="flex items-center gap-1 text-sm text-primary-500 font-medium">
                        <x-heroicon-o-ticket class="w-4 h-4"/>
                        {{ $record->requisition_number }}
                    </span>
                    <span class="text-sm text-gray-400 font-light">|</span>
                    <span class="flex items-center gap-1 text-sm text-gray-500">
                        {{ $record->site_reference }}
                    </span>
                </div>
                {{-- <span class="text-xl text-gray-700">
                    {{ $record->requisition_number }}
                </span> --}}
            </div>

            <div class="flex gap-2 pt-2">
                <div class="px-2 py-1 rounded text-xs text-white" style="background-color: green;">
                    {{ $record->is_processed? 'Pending' : 'Processed' }}
                </div>
                <div class="px-2 py-1 rounded text-xs text-white" style="background-color: Red;">
                    {{$record->lineItems->count()>50? 'High' : 'Low'}}
                </div>
                <div class="px-2 py-1 rounded text-xs  text-white" style="background-color: Blue;">
                    <span class="ml-2 capitalize">{{$record->projectsetting->name}}</span>
                </div>
            </div>

            <div class="pt-5">
                <span class="text-gray-500 dark:text-gray-300 text-sm font-medium">{{ __('Delivery address') }}</span>
                <div>{{$record->deliver_to}}</div>
                <span class="text-gray-500 dark:text-gray-300 text-sm font-medium">{{ __('Notes') }}</span>
                
                <div class="bg-gray-200 p-10 rounded-2xl"><div class=" text-gray-500 text-prose">{!! $record->notes !!}</div>
                    </div>
            </div>
        
    </div>

       <div class="col-span-1 sm:col-span-1 bg-white dark:bg-gray-900 shadow-sm p-10 space-y-5 rounded-lg">
            
            <div class="w-full flex flex-col gap-1" wire:ignore>
                <span class="text-gray-500 text-sm font-medium">
                    {{ __('Submitted by') }}
                </span>
                <div class="w-full flex items-center gap-1 text-gray-500">
                    @php($user = $record->creator)
                    {{-- @dd($user); --}}

                    @if($user)
                        <div>
                            @php($uniqid = uniqid())
                            
                            <img src={{$user->getAvatarUrl()}}
                                 alt="{{ $user->name }}"
                                 
                                 class="w-6 h-6 rounded-full  bg-cover bg-center"/>
                        </div>
                    @endif
                    {{ $record->creator->name }}
                </div>
            </div>
            <div class="w-full flex flex-col gap-1 mt-2" wire:ignore>
                <span class="text-gray-500 text-sm font-medium">
                    {{ __('Updated by') }}
                </span>
                <div class="w-full flex items-center gap-1 text-gray-500">
                    @php($user = $record->updator)
                    {{-- @dd($user); --}}
                    
                    
                    
                    @if($user)
                        <div>
                            @php($uniqid = uniqid())
                            
                            <img src={{$user->getAvatarUrl()}}
                                 alt="{{ $user->name }}"
                                 
                                 class="w-6 h-6 rounded-full  bg-cover bg-center"/>
                        </div>
                    @endif
                    {{ $record->updator? $record->updator->name : '-' }}
                </div>
            </div>
            <div class="w-full flex flex-col gap-1 mt-2" wire:ignore>
                <span class="text-gray-500 text-sm font-medium">
                    {{ __('Processed by') }}
                </span>
                <div class="w-full flex items-center gap-1 text-gray-500">
                    @php($user = $record->processor)
                    {{-- @dd($user); --}}
                    
                    
                    
                    @if($user)
                        <div>
                            @php($uniqid = uniqid())
                            
                            <img src={{$user->getAvatarUrl()}}
                                 alt="{{ $user->name }}"
                                 
                                 class="w-6 h-6 rounded-full  bg-cover bg-center"/>
                        </div>
                    @endif
                    {{ $record->processor ? $record->processor->name : '-' }}
                </div>
            </div>
            <div class="w-full flex flex-col gap-1 ">
                <span class="text-gray-500 text-sm font-medium">
                    {{ __('Creation date') }}
                </span>
                <div class="w-full text-gray-500">
                    {{ $record->created_at->format(__('Y-m-d g:i A')) }}
                    <span class="text-xs text-gray-400">
                        ({{ $record->created_at->diffForHumans() }})
                    </span>
                </div>
            </div>
            <div class="w-full flex flex-col gap-1 ">
                <span class="text-gray-500 text-sm font-medium">
                    {{ __('Last updated') }}
                </span>
                <div class="w-full text-gray-500">
                    {{ $record->updated_at->format(__('Y-m-d g:i A')) }}
                    <span class="text-xs text-gray-400">
                        ({{ $record->updated_at->diffForHumans() }})
                    </span>
                </div>
            </div>
           
        </div> 
        
    </div>

    <div class="w-full grid sm:grid-cols-3 gap-5">
        <div class="col-span-1 sm:col-span-2 bg-white dark:bg-gray-900 shadow-sm p-10 space-y-5 rounded-lg">
        <div class="text-sm font-medium  text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
            <button wire:click="selectTab('comments')"
                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300
                   @if($tab === 'comments') border-primary-500  text-primary-500 @else text-gray-700 @endif">
                {{ __('Comments') }}
            </button>
            <button wire:click="selectTab('activities')"
                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300
                    @if($tab === 'activities') inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active dark:text-blue-500 dark:border-blue-500 @else text-gray-700 @endif">
                {{ __('Activities') }}
            </button>
            <button wire:click="selectTab('items')"
                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300
                    @if($tab === 'items') inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active dark:text-blue-500 dark:border-blue-500 @else text-gray-700 @endif">
                {{ __('Requisition Items') }}
            </button>
            
        </div>
        @if ($tab === 'comments')
       
        @livewire('comment-box', ['record' => $record])
        @livewire('comment-list', ['record' => $record])


        {{-- @foreach($record->comments->sortByDesc('created_at') as $comment)
                    <div
                        class="w-full flex flex-col gap-2 @if(!$loop->last) pb-5 mb-5 border-b border-gray-200 @endif ticket-comment">
                        <div class="w-full flex justify-between">
                            <span class="flex items-center gap-1 text-gray-500 text-sm">
                                <span class="font-medium flex items-center gap-1">
                                   
                                    {{ $comment->user->name }}
                                </span>
                                <span class="text-gray-400 px-2">|</span>
                                {{ $comment->created_at->format('Y-m-d g:i A') }}
                                ({{ $comment->created_at->diffForHumans() }})
                            </span>
                            @if(  $comment->user_id === auth()->user()->id)
                                <div class="actions flex items-center gap-2">
                                    <button type="button" wire:click="editComment({{ $comment->id }})"
                                            class="text-primary-500 text-xs hover:text-primary-600 hover:underline">
                                        {{ __('Edit') }}
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <button type="button" wire:click="deleteComment({{ $comment->id }})"
                                            class="text-danger-500 text-xs hover:text-danger-600 hover:underline">
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="w-full prose">
                            {!! $comment->content !!}
                        </div>
                    </div>
                @endforeach --}}
        
@endif

        @if($tab === 'activities')
                <div class="w-full flex flex-col pt-5">
                    @if($record->activities->count())
                        @foreach($record->activities->sortByDesc('created_at') as $activity)
                            <div class="w-full flex flex-col gap-2
                                 @if(!$loop->last) pb-5 mb-5 border-b border-gray-200 @endif">
                                <span class="flex items-center gap-1 text-gray-500 text-sm">
                                    <span class="font-medium flex items-center gap-1">
                                        <img class="rounded-full w-8 h-8"src={{$activity->user->getAvatarUrl()}} alt={{$record->user}}>
                                        {{ $activity->user->name }}
                                    </span>
                                    <span class="text-gray-400 px-2">|</span>
                                    {{ $activity->created_at->format('Y-m-d g:i A') }}
                                    ({{ $activity->created_at->diffForHumans() }})
                                </span>
                                <div class="w-full flex items-center gap-10">
                                    <span style="color: {{ $activity->old_status_id ? 'green' : 'red' }}">
                                        {{ $activity->old_status_id? 'processed' : 'pending' }}
                                    </span>
                                    <x-heroicon-o-arrow-right class="w-6 h-6"/>
                                    <span style="color: {{ $activity->new_status_id ? 'green' : 'red' }}">
                                        {{ $activity->new_status_id ? 'processed' : 'pending' }}
                                    </span>
                                </div>
                                
                            </div>
                        @endforeach
                    @else
                        <span class="text-gray-400 text-sm font-medium">
                            {{ __('No activities yet!') }}
                        </span>
                    @endif
                </div>
            @endif
            @if($tab === 'items')
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-300 dark:text-gray-400">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">
                    Supplier Name
                </th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">
                    Item Code
                </th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">
                    Description
                </th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">
                    Qty
                </th>
            </tr>
        </thead>
        <tbody class=" divide-y">
            @foreach($record->lineItems as $item)
                <tr  class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-4 py-2">
                        {{ $record->supplier_name }}  <!-- Adjust this based on your actual column name -->
                    </td>
                    <td class="px-4 py-2">
                        {{ $item->item_code }}  <!-- Adjust this based on your actual column name -->
                    </td>
                    <td class="px-4 py-2">
                        {{ $item->description }}  <!-- Adjust this based on your actual column name -->
                    </td>
                    <td class="px-4 py-2">
                        {{ $item->qty }}  <!-- Adjust this based on your actual column name -->
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
            </div>
@endif

    </div>
    </div>
</x-filament::page>

@push('scripts')
    <script>
        window.addEventListener('shareRequisition', (e) => {
            const text = e.detail.url;
            const textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
            } catch (err) {
                console.error('Unable to copy to clipboard', err);
            }
            document.body.removeChild(textArea);
            new Notification().success().title('{{ __('Url copied to clipboard') }}').duration(6000).send();
        });

        document.getElementById('commentForm').addEventListener('keypress', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { // Check for Enter key without Shift
            e.preventDefault(); // Prevent the default new line in textarea
            this.submit(); // Submit the form
        }
    });
    </script>
@endpush
