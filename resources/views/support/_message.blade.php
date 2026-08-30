@php
    $isAdmin = $message->sender_type === 'admin';
    $senderName = $isAdmin
        ? 'Inveqi Support'
        : ($message->sender()->first_name ?: $message->sender()->name);
@endphp
<div class="flex {{ $isAdmin ? 'justify-start' : 'justify-end' }}" data-msg-id="{{ $message->id }}">
    <div class="{{ $isAdmin ? 'bg-white border border-gray-200 text-gray-800 rounded-2xl rounded-tl-none' : 'bg-blue-600 text-white rounded-2xl rounded-br-none' }} px-4 py-3 max-w-[75%] shadow-sm">
        <div class="text-xs font-semibold {{ $isAdmin ? 'text-gray-500 mb-1' : 'text-blue-200 mb-1' }}">
            {{ $senderName }}
        </div>
        <div class="whitespace-pre-wrap break-words">{{ $message->body }}</div>
        <span class="text-xs text-gray-400 mt-1 block">{{ $message->created_at->format('M d, g:i A') }}</span>
    </div>
</div>
