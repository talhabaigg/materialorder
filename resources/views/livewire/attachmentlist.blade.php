<div>
    @if ($record->attachments->isNotEmpty())
        <div class="p-2 bg-white dark:bg-gray-800 shadow rounded-lg break-words overflow-x-auto">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">List of attachments:</h2>
            <ul>
                @foreach ($record->attachments->sortByDesc('created_at') as $attachment)
                    <li class="mb-2">
                        <x-filament::link href="{{ Storage::disk('s3')->url($attachment->file_path) }}" target="_blank">
                            {{ $attachment->original_file_name }}
                        </x-filament::link>
                        <span> - Uploaded on {{ $attachment->created_at->format('d-m-Y g:i A') }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
