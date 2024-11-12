<div class="p-2 bg-white dark:bg-gray-800 shadow rounded-lg">
    <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Attachments</h2>

    @foreach($record->attachments->sortByDesc('created_at') as $attachment)
    <div class="">
        <!-- Display the original file name -->
        {{-- <p class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">File Name: {{ $attachment->original_file_name }}</p>
        
        <!-- Display a link to download the file -->
        <a href="{{ Storage::disk('s3')->url($attachment->file_path) }}" target="_blank" class="text-blue-500 hover:underline dark:text-blue-400">
            Download
        </a>

        <!-- Display the creation date -->
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Created on: {{ $attachment->created_at->format('F j, Y \a\t g:i A') }}
        </p> --}}
        <li>
            
            <a href="{{ Storage::disk('s3')->url($attachment->file_path) }}" target="_blank" class="text-blue-500 hover:underline dark:text-blue-400">
                {{ $attachment->original_file_name }}
            </a>
        </li>
       
    </div>
    @endforeach
</div>
