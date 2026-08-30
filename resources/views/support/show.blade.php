@extends('layouts.auth.app')

@section('title', $ticket->subject . ' - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    @php
        $priorityColors = [
            'low' => 'bg-gray-100 text-gray-700',
            'medium' => 'bg-blue-100 text-blue-700',
            'high' => 'bg-yellow-100 text-yellow-700',
            'urgent' => 'bg-red-100 text-red-700',
        ];
        $statusColors = [
            'open' => 'bg-blue-100 text-blue-700',
            'in_progress' => 'bg-yellow-100 text-yellow-700',
            'resolved' => 'bg-green-100 text-green-700',
            'closed' => 'bg-gray-100 text-gray-600',
        ];
    @endphp

    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('support.index') }}" class="text-sm text-blue-600 hover:underline">
                <i class="fas fa-arrow-left mr-1"></i> Back to tickets
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $ticket->subject }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-2 py-1 rounded-full text-xs capitalize {{ $priorityColors[$ticket->priority] ?? 'bg-gray-100 text-gray-700' }}">{{ $ticket->priority }}</span>
            <span class="px-2 py-1 rounded-full text-xs capitalize {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-600' }}">{{ str_replace('_', ' ', $ticket->status) }}</span>
        </div>
    </div>

    <div class="bg-white shadow rounded-xl border border-gray-100 overflow-hidden flex flex-col" style="height: calc(100vh - 260px);">
        {{-- Messages --}}
        <div id="chatBox" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
            @forelse($messages as $message)
                @include('support._message', ['message' => $message])
            @empty
                <div class="text-center text-gray-400 py-10">
                    No messages yet. Describe your issue and a support agent will respond.
                </div>
            @endforelse
        </div>

        {{-- Input --}}
        <div class="border-t border-gray-200 p-4 bg-white">
            @if($ticket->status === 'closed')
                <div class="text-center text-sm text-gray-500 py-2">This ticket is closed.</div>
            @else
                <form id="messageForm" class="flex items-end gap-3">
                    @csrf
                    <textarea id="messageInput" rows="1" required
                              class="flex-1 border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none"
                              placeholder="Type your message..."></textarea>
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 inline-flex items-center">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <script>
        (function () {
            const chatBox = document.getElementById('chatBox');
            const form = document.getElementById('messageForm');
            const input = document.getElementById('messageInput');
            const ticketId = {{ $ticket->id }};

            function escapeHtml(s) {
                const div = document.createElement('div');
                div.textContent = s;
                return div.innerHTML;
            }

            function timeHtml(time) {
                return '<span class="text-xs text-gray-400 mt-1 block">' + escapeHtml(time) + '</span>';
            }

            function messageHtml(msg) {
                const isAdmin = msg.is_admin;
                const bubble = isAdmin
                    ? '<div class="bg-white border border-gray-200 text-gray-800 rounded-2xl rounded-tl-none px-4 py-3 max-w-[75%] shadow-sm">'
                    : '<div class="bg-blue-600 text-white rounded-2xl rounded-br-none px-4 py-3 max-w-[75%]">';
                const sender = '<div class="text-xs font-semibold ' + (isAdmin ? 'text-gray-500 mb-1' : 'text-blue-200 mb-1') + '">' + escapeHtml(msg.sender) + '</div>';
                return '<div class="flex ' + (isAdmin ? 'justify-start' : 'justify-end') + '">' +
                    bubble + sender +
                    '<div class="whitespace-pre-wrap break-words">' + escapeHtml(msg.body) + '</div>' +
                    timeHtml(msg.time) +
                    '</div></div>';
            }

            function scrollBottom() {
                chatBox.scrollTop = chatBox.scrollHeight;
            }

            // Auto-grow textarea
            if (input) {
                input.addEventListener('input', function () {
                    input.style.height = 'auto';
                    input.style.height = Math.min(input.scrollHeight, 150) + 'px';
                });
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        form.dispatchEvent(new Event('submit', { cancelable: true }));
                    }
                });
            }

            let lastId = 0;
            document.querySelectorAll('#chatBox [data-msg-id]').forEach(function (el) {
                const id = parseInt(el.getAttribute('data-msg-id'), 10);
                if (id > lastId) lastId = id;
            });

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const body = input.value.trim();
                    if (!body) return;

                    fetch('{{ route("support.messages.store", $ticket) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                                '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ message: body }),
                    })
                    .then(r => r.json())
                    .then(function (data) {
                        input.value = '';
                        input.style.height = 'auto';
                        const msg = { id: data.id, body: data.body, time: data.time, sender: data.sender, is_admin: false };
                        chatBox.insertAdjacentHTML('beforeend', messageHtml(msg));
                        if (data.id > lastId) lastId = data.id;
                        scrollBottom();
                    })
                    .catch(function () { alert('Could not send message.'); });
                });
            }

            // Poll for new messages every 4 seconds
            setInterval(function () {
                fetch('{{ route("support.messages.poll", $ticket) }}?after_id=' + lastId, {
                    headers: { 'Accept': 'application/json' },
                })
                .then(r => r.json())
                .then(function (data) {
                    (data.messages || []).forEach(function (msg) {
                        chatBox.insertAdjacentHTML('beforeend', messageHtml(msg));
                        if (msg.id > lastId) lastId = msg.id;
                    });
                    if (data.messages && data.messages.length) scrollBottom();
                })
                .catch(function () {});
            }, 4000);

            scrollBottom();
        })();
    </script>
@endsection
