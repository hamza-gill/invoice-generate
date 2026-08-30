@extends('layouts.auth.app')

@section('title', $ticket->subject . ' - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    @php
        $priorityStyles = [
            'low'    => ['badge' => 'bg-slate-100 text-slate-600',  'dot' => 'bg-slate-400',  'icon' => 'fa-circle-arrow-down'],
            'medium' => ['badge' => 'bg-blue-100 text-blue-700',    'dot' => 'bg-blue-500',   'icon' => 'fa-circle-minus'],
            'high'   => ['badge' => 'bg-orange-100 text-orange-700','dot' => 'bg-orange-500', 'icon' => 'fa-circle-up'],
            'urgent' => ['badge' => 'bg-red-100 text-red-700',      'dot' => 'bg-red-500',    'icon' => 'fa-triangle-exclamation'],
        ];
        $statusStyles = [
            'open'        => ['badge' => 'bg-blue-100 text-blue-700',   'dot' => 'bg-blue-500'],
            'in_progress' => ['badge' => 'bg-yellow-100 text-yellow-700','dot' => 'bg-yellow-500'],
            'resolved'    => ['badge' => 'bg-green-100 text-green-700', 'dot' => 'bg-green-500'],
            'closed'      => ['badge' => 'bg-gray-100 text-gray-600',   'dot' => 'bg-gray-400'],
        ];
        $pStyle = $priorityStyles[$ticket->priority] ?? $priorityStyles['medium'];
        $sStyle = $statusStyles[$ticket->status] ?? $statusStyles['open'];

        // Group messages by calendar day for date-separator dividers, without
        // touching the existing support._message partial's own markup.
        $groupedMessages = $messages->groupBy(function ($m) {
            return \Carbon\Carbon::parse($m->created_at)->format('Y-m-d');
        });
    @endphp

    <div class="max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="min-w-0">
                    <a href="{{ route('support.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-2">
                        <i class="fas fa-arrow-left text-xs"></i> Back to tickets
                    </a>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-800 truncate">{{ $ticket->subject }}</h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-400 mt-1.5">
                        @if($ticket->number ?? null)
                            <span>Ticket #{{ $ticket->number }}</span>
                        @else
                            <span>Ticket #{{ $ticket->id }}</span>
                        @endif
                        <span><i class="fas fa-clock mr-1"></i>Opened {{ \Carbon\Carbon::parse($ticket->created_at)->diffForHumans() }}</span>
                        @if($ticket->updated_at)
                            <span><i class="fas fa-rotate mr-1"></i>Updated {{ \Carbon\Carbon::parse($ticket->updated_at)->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium capitalize {{ $pStyle['badge'] }}">
                        <i class="fas {{ $pStyle['icon'] }} text-[10px]"></i>{{ $ticket->priority }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium capitalize {{ $sStyle['badge'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $sStyle['dot'] }}"></span>{{ str_replace('_', ' ', $ticket->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Chat card --}}
        <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden flex flex-col relative" style="height: calc(100vh - 320px); min-height: 420px;">

            {{-- Messages --}}
            <div id="chatBox" class="flex-1 overflow-y-auto p-6 space-y-1 bg-gray-50">
                @forelse($groupedMessages as $day => $dayMessages)
                    <div class="flex items-center justify-center my-4 first:mt-0">
                        <span class="text-[11px] font-medium text-gray-400 bg-gray-200/60 px-3 py-1 rounded-full">
                            {{ \Carbon\Carbon::parse($day)->isToday() ? 'Today' : \Carbon\Carbon::parse($day)->format('M d, Y') }}
                        </span>
                    </div>
                    @foreach($dayMessages as $message)
                        <div class="pb-4">
                            @include('support._message', ['message' => $message])
                        </div>
                    @endforeach
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center text-gray-400 py-10">
                        <i class="fas fa-comments text-4xl text-gray-200 mb-3"></i>
                        <p class="text-sm">No messages yet.</p>
                        <p class="text-xs text-gray-400 mt-1">Describe your issue below and a support agent will respond.</p>
                    </div>
                @endforelse
            </div>

            {{-- Jump-to-latest button (shown when scrolled up) --}}
            <button type="button" id="jumpToLatest" class="hidden absolute bottom-24 right-6 bg-white border border-gray-200 shadow-md rounded-full px-4 py-2 text-xs font-medium text-gray-600 hover:text-blue-600 hover:border-blue-300 transition items-center gap-1.5">
                <i class="fas fa-arrow-down text-[10px]"></i> New messages
            </button>

            {{-- Input --}}
            <div class="border-t border-gray-200 p-4 bg-white">
                @if($ticket->status === 'closed')
                    <div class="text-center text-sm text-gray-500 py-2">
                        <i class="fas fa-lock mr-1.5 text-gray-400"></i>This ticket is closed.
                    </div>
                @else
                    <div id="sendError" class="hidden mb-2 text-xs text-red-600 bg-red-50 border border-red-100 rounded-lg px-3 py-2 items-center justify-between">
                        <span><i class="fas fa-circle-exclamation mr-1"></i> Could not send your message.</span>
                        <button type="button" id="retryBtn" class="font-semibold underline hover:no-underline">Retry</button>
                    </div>

                    <form id="messageForm" enctype="multipart/form-data">
                        @csrf

                        {{-- Attachment chips --}}
                        <div id="attachmentChips" class="hidden flex-wrap gap-2 mb-2"></div>

                        <div id="messageDropzone" class="flex items-end gap-3 border border-gray-200 rounded-xl p-2 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition">
                            <div class="flex-1 flex flex-col">
                                <textarea id="messageInput" rows="1" required
                                          class="flex-1 border-0 focus:ring-0 focus:outline-none resize-none px-2 py-2 text-sm"
                                          placeholder="Type your message... (Shift+Enter for a new line)"></textarea>
                                <div class="flex items-center gap-1 px-2 pb-1">
                                    <label for="attachmentInput" class="cursor-pointer inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-blue-600 transition">
                                        <i class="fas fa-paperclip"></i> Attach files
                                    </label>
                                    <input type="file" id="attachmentInput" multiple class="hidden"
                                           accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.zip,.ppt,.pptx">
                                </div>
                            </div>
                            <button type="submit" id="sendBtn" class="bg-blue-600 text-white w-11 h-11 rounded-lg hover:bg-blue-700 inline-flex items-center justify-center transition disabled:opacity-50 disabled:cursor-not-allowed shrink-0">
                                <i class="fas fa-paper-plane" id="sendIcon"></i>
                                <i class="fas fa-circle-notch fa-spin hidden" id="sendSpinner"></i>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <style>
        #messageDropzone.dragover { border-color: #3B82F6; background-color: rgba(59, 130, 246, 0.05); }
        #chatBox { scroll-behavior: smooth; }
    </style>

    <script>
        (function () {
            const chatBox = document.getElementById('chatBox');
            const form = document.getElementById('messageForm');
            if (!form) { return; } // ticket is closed — nothing else to wire up

            const input = document.getElementById('messageInput');
            const fileInput = document.getElementById('attachmentInput');
            const attachmentChips = document.getElementById('attachmentChips');
            const dropzone = document.getElementById('messageDropzone');
            const sendBtn = document.getElementById('sendBtn');
            const sendIcon = document.getElementById('sendIcon');
            const sendSpinner = document.getElementById('sendSpinner');
            const sendError = document.getElementById('sendError');
            const retryBtn = document.getElementById('retryBtn');
            const jumpToLatest = document.getElementById('jumpToLatest');
            const ticketId = {{ $ticket->id }};

            let pendingFiles = [];
            let lastSentPayload = null;

            const ICONS = {
                pdf: 'fa-file-pdf text-red-500',
                doc: 'fa-file-word text-blue-500', docx: 'fa-file-word text-blue-500',
                xls: 'fa-file-excel text-green-600', xlsx: 'fa-file-excel text-green-600', csv: 'fa-file-csv text-green-600',
                ppt: 'fa-file-powerpoint text-orange-500', pptx: 'fa-file-powerpoint text-orange-500',
                zip: 'fa-file-zipper text-amber-500',
                txt: 'fa-file-lines text-gray-500',
                jpg: 'fa-file-image text-purple-500', jpeg: 'fa-file-image text-purple-500',
                png: 'fa-file-image text-purple-500', gif: 'fa-file-image text-purple-500', webp: 'fa-file-image text-purple-500',
            };

            function iconFor(name) {
                const ext = (name.split('.').pop() || '').toLowerCase();
                return ICONS[ext] || 'fa-file text-gray-400';
            }

            function escapeHtml(s) {
                const div = document.createElement('div');
                div.textContent = s;
                return div.innerHTML;
            }

            function timeHtml(time) {
                return '<span class="text-xs text-gray-400 mt-1 block">' + escapeHtml(time) + '</span>';
            }

            function attachmentsHtml(atts) {
                if (!atts || !atts.length) return '';
                const items = (atts || []).map(function (a) {
                    const icon = a.is_image ? 'fa-image' : 'fa-paperclip';
                    return '<a href="' + a.download_url + '" target="_blank" class="inline-flex items-center gap-1.5 bg-white/30 hover:bg-white/40 rounded-lg px-2 py-1 text-xs">' +
                        '<i class="fas ' + icon + '"></i>' +
                        '<span style="max-width:140px" class="truncate">' + escapeHtml(a.original_name) + '</span>' +
                        '<span class="opacity-75">(' + escapeHtml(a.human_size || '') + ')</span>' +
                        '</a>';
                }).join('');
                return '<div class="flex flex-wrap gap-2 mt-2">' + items + '</div>';
            }

            function messageHtml(msg) {
                const isAdmin = msg.is_admin;
                const bubble = isAdmin
                    ? '<div class="bg-white border border-gray-200 text-gray-800 rounded-2xl rounded-tl-none px-4 py-3 max-w-[75%] shadow-sm">'
                    : '<div class="bg-blue-600 text-white rounded-2xl rounded-br-none px-4 py-3 max-w-[75%]">';
                const sender = '<div class="text-xs font-semibold ' + (isAdmin ? 'text-gray-500 mb-1' : 'text-blue-200 mb-1') + '">' + escapeHtml(msg.sender) + '</div>';
                return '<div class="flex ' + (isAdmin ? 'justify-start' : 'justify-end') + ' pb-4" data-msg-id="' + (msg.id || '') + '">' +
                    bubble + sender +
                    '<div class="whitespace-pre-wrap break-words">' + escapeHtml(msg.body) + '</div>' +
                    attachmentsHtml(msg.attachments) +
                    timeHtml(msg.time) +
                    '</div></div>';
            }

            function pendingMessageHtml(tempId, body, files) {
                const fileChips = files.length
                    ? '<div class="flex flex-wrap gap-1.5 mt-2 opacity-80">' + files.map(f =>
                    '<span class="inline-flex items-center gap-1 bg-white/20 rounded px-1.5 py-0.5 text-[11px]"><i class="fas ' + iconFor(f.name) + '"></i>' + escapeHtml(f.name) + '</span>'
                ).join('') + '</div>'
                    : '';
                return '<div class="flex justify-end pb-4 opacity-60" data-temp-id="' + tempId + '">' +
                    '<div class="bg-blue-600 text-white rounded-2xl rounded-br-none px-4 py-3 max-w-[75%]">' +
                    '<div class="whitespace-pre-wrap break-words">' + escapeHtml(body) + '</div>' +
                    fileChips +
                    '<span class="text-xs text-blue-100 mt-1 flex items-center gap-1"><i class="fas fa-circle-notch fa-spin"></i> Sending&hellip;</span>' +
                    '</div></div>';
            }

            function isNearBottom() {
                return chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < 120;
            }

            function scrollBottom(force) {
                if (force || isNearBottom()) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                    jumpToLatest.classList.add('hidden');
                }
            }

            chatBox.addEventListener('scroll', function () {
                if (isNearBottom()) jumpToLatest.classList.add('hidden');
            });

            jumpToLatest.addEventListener('click', function () {
                scrollBottom(true);
            });

            // Auto-grow textarea
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

            // Attachment chips (click-to-add)
            function renderChips() {
                if (!pendingFiles.length) {
                    attachmentChips.classList.add('hidden');
                    attachmentChips.innerHTML = '';
                    return;
                }
                attachmentChips.classList.remove('hidden');
                attachmentChips.classList.add('flex');
                attachmentChips.innerHTML = pendingFiles.map((file, i) => `
                    <span class="inline-flex items-center gap-1.5 bg-gray-100 border border-gray-200 rounded-lg pl-2 pr-1 py-1 text-xs text-gray-700">
                        <i class="fas ${iconFor(file.name)}"></i>
                        <span class="max-w-[140px] truncate">${escapeHtml(file.name)}</span>
                        <button type="button" data-i="${i}" class="remove-chip w-4 h-4 rounded-full hover:bg-gray-300 flex items-center justify-center text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-[10px]"></i>
                        </button>
                    </span>
                `).join('');

                attachmentChips.querySelectorAll('.remove-chip').forEach(btn => {
                    btn.addEventListener('click', () => {
                        pendingFiles.splice(parseInt(btn.dataset.i), 1);
                        syncFileInput();
                        renderChips();
                    });
                });
            }

            function syncFileInput() {
                const dt = new DataTransfer();
                pendingFiles.forEach(f => dt.items.add(f));
                fileInput.files = dt.files;
            }

            function addFiles(fileListLike) {
                Array.from(fileListLike).forEach(file => {
                    if (!pendingFiles.some(f => f.name === file.name && f.size === file.size)) {
                        pendingFiles.push(file);
                    }
                });
                syncFileInput();
                renderChips();
            }

            fileInput.addEventListener('change', () => addFiles(fileInput.files));

            ['dragenter', 'dragover'].forEach(evt => dropzone.addEventListener(evt, e => { e.preventDefault(); dropzone.classList.add('dragover'); }));
            ['dragleave', 'drop'].forEach(evt => dropzone.addEventListener(evt, e => { e.preventDefault(); dropzone.classList.remove('dragover'); }));
            dropzone.addEventListener('drop', e => { if (e.dataTransfer?.files?.length) addFiles(e.dataTransfer.files); });

            let lastId = 0;
            document.querySelectorAll('#chatBox [data-msg-id]').forEach(function (el) {
                const id = parseInt(el.getAttribute('data-msg-id'), 10);
                if (id > lastId) lastId = id;
            });

            function setSending(isSending) {
                sendBtn.disabled = isSending;
                sendIcon.classList.toggle('hidden', isSending);
                sendSpinner.classList.toggle('hidden', !isSending);
            }

            function doSend(body, files) {
                sendError.classList.add('hidden');
                sendError.classList.remove('flex');
                setSending(true);

                const tempId = 'temp-' + Date.now();
                chatBox.insertAdjacentHTML('beforeend', pendingMessageHtml(tempId, body, files));
                scrollBottom(true);

                const formData = new FormData();
                formData.append('message', body);
                for (let i = 0; i < files.length; i++) {
                    formData.append('attachments[]', files[i]);
                }

                lastSentPayload = { body, files };

                fetch('{{ route("support.messages.store", $ticket) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                    .then(r => { if (!r.ok) throw new Error('Request failed'); return r.json(); })
                    .then(function (data) {
                        document.querySelector('[data-temp-id="' + tempId + '"]')?.remove();
                        const msg = { id: data.id, body: data.body, time: data.time, sender: data.sender, is_admin: false, attachments: data.attachments };
                        chatBox.insertAdjacentHTML('beforeend', messageHtml(msg));
                        if (data.id > lastId) lastId = data.id;
                        scrollBottom(true);
                        setSending(false);
                        lastSentPayload = null;
                    })
                    .catch(function () {
                        document.querySelector('[data-temp-id="' + tempId + '"]')?.remove();
                        sendError.classList.remove('hidden');
                        sendError.classList.add('flex');
                        setSending(false);
                        // Restore the message so the user doesn't lose what they typed
                        input.value = body;
                        input.style.height = 'auto';
                        input.style.height = Math.min(input.scrollHeight, 150) + 'px';
                        pendingFiles = files;
                        syncFileInput();
                        renderChips();
                    });
            }

            retryBtn.addEventListener('click', function () {
                if (lastSentPayload) {
                    doSend(lastSentPayload.body, lastSentPayload.files);
                }
            });

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const body = input.value.trim();
                if (!body && !pendingFiles.length) return;

                const filesToSend = pendingFiles.slice();

                input.value = '';
                input.style.height = 'auto';
                pendingFiles = [];
                syncFileInput();
                renderChips();

                doSend(body, filesToSend);
            });

            // Poll for new messages every 4 seconds
            setInterval(function () {
                fetch('{{ route("support.messages.poll", $ticket) }}?after_id=' + lastId, {
                    headers: { 'Accept': 'application/json' },
                })
                    .then(r => r.json())
                    .then(function (data) {
                        if (data.messages && data.messages.length) {
                            const wasNearBottom = isNearBottom();
                            data.messages.forEach(function (msg) {
                                chatBox.insertAdjacentHTML('beforeend', messageHtml(msg));
                                if (msg.id > lastId) lastId = msg.id;
                            });
                            if (wasNearBottom) {
                                scrollBottom(true);
                            } else {
                                jumpToLatest.classList.remove('hidden');
                                jumpToLatest.classList.add('flex');
                            }
                        }
                    })
                    .catch(function () {});
            }, 4000);

            scrollBottom(true);
        })();
    </script>
@endsection
