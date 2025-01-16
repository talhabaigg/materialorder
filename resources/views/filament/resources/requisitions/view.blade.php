<x-filament::page>
    <a wire:navigate href="{{ \App\Filament\Resources\RequisitionResource::getUrl() }}"
        class="flex items-center gap-1 text-gray-500 hover:text-gray-700 font-medium text-xs">
        <x-heroicon-o-arrow-left class="w-4 h-4" /> {{ __('Back to requisition page') }}
    </a>


    <div class="grid sm:grid-cols-3 gap-5">
        <div class="col-span-1 sm:col-span-2 bg-white dark:bg-gray-900 shadow-sm p-10 rounded-lg">



            <div class="flex justify-between items-center gap-2 pt-2 mb-2">
                <div class="">
                    <div class="flex items-center gap-2">
                        <span class="flex items-center gap-1 text-sm text-primary-500 font-medium">
                            <x-heroicon-o-ticket class="w-4 h-4" />
                            {{ $record->requisition_number }}
                        </span>
                        <span class="text-sm text-gray-400 font-light">|</span>
                        <span class="flex items-center gap-1 text-sm text-gray-500">
                            {{ $record->site_reference }}
                        </span>
                    </div>
                    <div class="flex justify-start gap-2 mt-2">
                        <div
                            class="px-2 py-1 rounded text-xs text-white {{ $record->is_processed ? 'bg-green-500' : 'bg-yellow-500' }}">
                            {{ $record->is_processed ? 'Processed' : 'Pending' }}
                        </div>
                        <div
                            class="px-2 py-1 rounded text-xs text-white  {{ $record->lineItems->count() > 50 ? 'bg-red-600' : 'bg-gray-500' }}">
                            {{ $record->lineItems->count() > 50 ? 'High' : 'Low' }}
                        </div>
                        <div class="px-2 py-1 rounded text-xs text-white bg-blue-500" style="background-color: Blue;">
                            <span class="">
                                <a href="/admin/projects/{{ $record->project_id }}/view" class="capitalize"
                                    target="_blank">
                                    {{ $record->projectsetting->name }}
                                </a>
                            </span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('requisition.excel', ['requisition' => $record->id]) }}"
                    class="flex items-center px-2 py-1 h-8 rounded text-xs bg-gray-500 hover:bg-gray-700 text-white dark:bg-gray-700 hover:dark:bg-gray-800 hover:shadow-lg">
                    <img src="/button-icons/premier-logo.png" alt="Premier Logo" class="w-8 mr-2">
                    Download
                </a>

            </div>

            <div class="pt-5">
                <span class="text-gray-500 dark:text-gray-300 text-sm font-medium">{{ __('Delivery address') }}</span>
                <div>{{ $record->deliver_to }}</div>
                <span class="text-gray-500 dark:text-gray-300 text-sm font-medium">{{ __('Notes') }}</span>

                <div class="">
                    <div class=" text-gray-500 dark:text-gray-300 text-prose break-words">{!! $record->notes !!}</div>
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

                    @if ($user)
                        <div>
                            @php($uniqid = uniqid())

                            <img src={{ $user->getAvatarUrl() }} alt="{{ $user->name }}"
                                class="w-6 h-6 rounded-full  bg-cover bg-center" />
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



                    @if ($user)
                        <div>
                            @php($uniqid = uniqid())

                            <img src={{ $user->getAvatarUrl() }} alt="{{ $user->name }}"
                                class="w-6 h-6 rounded-full  bg-cover bg-center" />
                        </div>
                    @endif
                    {{ $record->updator ? $record->updator->name : '-' }}
                </div>
            </div>
            <div class="w-full flex flex-col gap-1 mt-2" wire:ignore>
                <span class="text-gray-500 text-sm font-medium">
                    {{ __('Processed by') }}
                </span>
                <div class="w-full flex items-center gap-1 text-gray-500">
                    @php($user = $record->processor)
                    {{-- @dd($user); --}}



                    @if ($user)
                        <div>
                            @php($uniqid = uniqid())

                            <img src={{ $user->getAvatarUrl() }} alt="{{ $user->name }}"
                                class="w-6 h-6 rounded-full  bg-cover bg-center" />
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

    <div class="grid-cols-1 grid sm:grid-cols-3 gap-5">
        <div class="col-span-1 sm:col-span-2 bg-white dark:bg-gray-900 shadow-sm p-10 space-y-5 rounded-lg">
            <div
                class="text-sm font-medium  text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
                <button wire:click="selectTab('comments')"
                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300
                   @if ($tab === 'comments') inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active dark:text-blue-500 dark:border-blue-500 @else text-gray-700 @endif">
                    {{ __('Comments') }}
                </button>
                <button wire:click="selectTab('activities')"
                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300
                    @if ($tab === 'activities') inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active dark:text-blue-500 dark:border-blue-500 @else text-gray-700 @endif">
                    {{ __('Activities') }}
                </button>
                <button wire:click="selectTab('items')"
                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300
                    @if ($tab === 'items') inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active dark:text-blue-500 dark:border-blue-500 @else text-gray-700 @endif">
                    {{ __('Items') }}
                </button>
                <button wire:click="selectTab('attachments')"
                    class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300
            @if ($tab === 'attachments') inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active dark:text-blue-500 dark:border-blue-500 @else text-gray-700 @endif">
                    {{ __('Attachments') }}
                </button>

            </div>
            @if ($tab === 'comments')
                @livewire('comment-box', ['record' => $record])
                @livewire('comment-list', ['record' => $record])
            @endif

            @if ($tab === 'activities')
                <div class="w-full flex flex-col pt-5">
                    @if ($record->activities->count())
                        @foreach ($record->activities->sortByDesc('created_at') as $activity)
                            <div
                                class="w-full flex flex-col gap-2
                                 @if (!$loop->last) pb-5 mb-5 border-b border-gray-200 @endif">
                                <span class="flex items-center gap-1 text-gray-500 text-sm">
                                    <span class="font-medium flex items-center gap-1">
                                        <img class="rounded-full w-8 h-8"src={{ $activity->user->getAvatarUrl() }}
                                            alt={{ $record->user }}>
                                        {{ $activity->user->name }}
                                    </span>
                                    <span class="text-gray-400 px-2">|</span>
                                    {{ $activity->created_at->format('Y-m-d g:i A') }}
                                    ({{ $activity->created_at->diffForHumans() }})
                                </span>
                                <div class="w-full flex items-center gap-10">
                                    <span style="color: {{ $activity->old_status_id ? 'green' : 'red' }}">
                                        {{ $activity->old_status_id ? 'processed' : 'pending' }}
                                    </span>
                                    <x-heroicon-o-arrow-right class="w-6 h-6" />
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

            @if ($tab === 'items')
                @livewire('requisition-items-table', ['record' => $record])
            @endif
            @if ($tab === 'attachments')
                @livewire('attachment', ['record' => $record])
                @livewire('attachmentlist', ['record' => $record])
            @endif
        </div>
    </div>
</x-filament::page>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const commentForm = document.getElementById('commentForm');
            if (commentForm) {
                commentForm.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) { // Check for Enter key without Shift
                        e.preventDefault(); // Prevent the default new line in textarea
                        this.submit(); // Submit the form
                    }
                });
            }
        });
    </script>
@endpush
