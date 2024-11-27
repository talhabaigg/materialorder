<div>


    @foreach ($record->comments->sortByDesc('created_at') as $comment)
        <div
            class="w-full flex flex-col gap-2 @if (!$loop->last) pb-5 mb-5 border-b border-gray-200 @endif ticket-comment">
            <div class="w-full flex justify-between">
                <span class="flex items-center gap-1 text-gray-500 text-sm">
                    <img src={{ $comment->user->getAvatarUrl() }} alt="{{ $comment->user->name }}"
                        class="w-6 h-6 rounded-full  bg-cover bg-center" />
                    <span class="font-medium flex items-center gap-1">{{ $comment->user->name }}</span>
                    <span class="text-gray-400 px-2">|</span>
                    {{ $comment->created_at->format('Y-m-d g:i A') }}
                    ({{ $comment->created_at->diffForHumans() }})
                </span>
                @if (auth()->user()->id === $comment->user_id)
                    <div class="actions flex items-center gap-2">
                        <button type="button" wire:click="editComment({{ $comment->id }})"
                            class="text-primary-500 text-xs hover:text-primary-600 hover:underline">{{ __('Edit') }}</button>
                        <span class="text-gray-300">|</span>
                        <button type="button" wire:click="deleteComment({{ $comment->id }})"
                            class="text-danger-500 text-xs hover:text-danger-600 hover:underline">{{ __('Delete') }}</button>
                    </div>
                @endif
            </div>
            <div class="w-full prose text-gray-400">{!! $comment->content !!}</div>
        </div>
    @endforeach

    <!-- Editing Comment Input -->
    @if ($commentId)
        <div class="mt-4">
            <textarea wire:model="newCommentContent"class="w-full p-2 border rounded-xl bg-gray-100 text-gray-700" rows="2"></textarea>
            <button wire:click="updateComment"
                class="px-3 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded mt-3">Update Comment</button>
        </div>
    @endif
</div>
