@extends('layouts.auth.app')

@section('title', 'New Support Ticket - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('support.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-2">
                    <i class="fas fa-arrow-left text-xs"></i> Back to tickets
                </a>
                <h1 class="text-2xl font-bold text-gray-800">New Support Ticket</h1>
                <p class="text-sm text-gray-500 mt-1">Tell us what's going on and we'll get back to you as soon as we can.</p>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <div class="flex items-center gap-2 font-medium mb-1">
                    <i class="fas fa-circle-exclamation"></i> Please fix the following:
                </div>
                <ul class="list-disc list-inside text-sm space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            {{-- FORM --}}
            <div class="lg:col-span-2 bg-white shadow-sm rounded-2xl border border-gray-100 p-6 sm:p-8">
                <form id="ticketForm" action="{{ route('support.store') }}" method="POST" class="space-y-7" enctype="multipart/form-data">
                    @csrf

                    {{-- SUBJECT --}}
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                        <div class="relative">
                            <i class="fas fa-tag absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required maxlength="150"
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition"
                                   placeholder="Briefly describe the issue">
                        </div>
                    </div>

                    {{-- PRIORITY --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Priority *</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @php
                                $priorities = [
                                    'low'    => ['label' => 'Low',    'icon' => 'fa-circle-arrow-down', 'ring' => 'peer-checked:ring-slate-300 peer-checked:border-slate-400',  'dot' => 'bg-slate-400',  'text' => 'text-slate-500'],
                                    'medium' => ['label' => 'Medium', 'icon' => 'fa-circle-minus',       'ring' => 'peer-checked:ring-blue-200 peer-checked:border-blue-500',   'dot' => 'bg-blue-500',   'text' => 'text-blue-600'],
                                    'high'   => ['label' => 'High',   'icon' => 'fa-circle-up',          'ring' => 'peer-checked:ring-orange-200 peer-checked:border-orange-500', 'dot' => 'bg-orange-500', 'text' => 'text-orange-600'],
                                    'urgent' => ['label' => 'Urgent', 'icon' => 'fa-triangle-exclamation','ring' => 'peer-checked:ring-red-200 peer-checked:border-red-500',    'dot' => 'bg-red-500',    'text' => 'text-red-600'],
                                ];
                                $selectedPriority = old('priority', 'medium');
                            @endphp
                            @foreach($priorities as $value => $p)
                                <label class="cursor-pointer">
                                    <input type="radio" name="priority" value="{{ $value }}" class="hidden peer" {{ $selectedPriority === $value ? 'checked' : '' }} required>
                                    <div class="relative border-2 border-gray-200 rounded-xl p-3.5 text-center transition-all hover:border-gray-300 peer-checked:ring-2 {{ $p['ring'] }}">
                                        @if($value === 'urgent')
                                            <span class="absolute -top-1.5 -right-1.5 flex h-3 w-3">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                            </span>
                                        @endif
                                        <i class="fas {{ $p['icon'] }} {{ $p['text'] }} text-lg mb-1.5"></i>
                                        <p class="text-sm font-semibold text-gray-700">{{ $p['label'] }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2.5">
                            <i class="fas fa-circle-info mr-1"></i>
                            Use <span class="font-semibold text-red-600">Urgent</span> only if this is blocking your work right now.
                        </p>
                    </div>

                    {{-- MESSAGE --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="message" class="block text-sm font-medium text-gray-700">Message *</label>
                            <span id="messageCount" class="text-xs text-gray-400">0 characters</span>
                        </div>
                        <textarea id="message" name="message" rows="7" required minlength="10"
                                  class="w-full border border-gray-300 rounded-lg p-3.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition resize-y"
                                  placeholder="Describe the issue in detail, including any steps to reproduce.">{{ old('message') }}</textarea>
                        <p class="text-xs text-gray-500 mt-2">The more detail you give us, the faster we can help — what you expected, what happened instead, and when it started.</p>
                    </div>

                    {{-- ATTACHMENTS --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Attachments <span class="text-gray-400 font-normal">(optional, up to 5 files, 10MB each)</span>
                        </label>

                        <div id="dropzone" class="relative border-2 border-dashed border-gray-300 rounded-xl px-6 py-8 text-center hover:border-blue-400 hover:bg-blue-50/40 transition cursor-pointer">
                            <input type="file" id="attachments" name="attachments[]" multiple
                                   accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.zip,.ppt,.pptx"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <i class="fas fa-cloud-arrow-up text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-600"><span class="font-semibold text-blue-600">Click to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-1">Screenshots, logs, or documents that help us diagnose the issue</p>
                        </div>

                        <div id="fileErrors" class="hidden mt-2 text-xs text-red-600 space-y-1"></div>

                        <div id="fileList" class="mt-3 space-y-2"></div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('support.index') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 font-medium inline-flex items-center transition disabled:opacity-60 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane mr-2"></i>
                            <span id="submitBtnText">Submit Ticket</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- SIDEBAR --}}
            <div class="space-y-4">
                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-clock text-blue-500"></i> Typical response time
                    </h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-gray-600"><span class="w-2 h-2 rounded-full bg-red-500"></span>Urgent</span>
                            <span class="font-medium text-gray-800">Within 1 hour</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-gray-600"><span class="w-2 h-2 rounded-full bg-orange-500"></span>High</span>
                            <span class="font-medium text-gray-800">Within 4 hours</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-gray-600"><span class="w-2 h-2 rounded-full bg-blue-500"></span>Medium</span>
                            <span class="font-medium text-gray-800">Within 1 business day</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="flex items-center gap-2 text-gray-600"><span class="w-2 h-2 rounded-full bg-slate-400"></span>Low</span>
                            <span class="font-medium text-gray-800">Within 2-3 business days</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-lightbulb text-amber-400"></i> Tips for a faster reply
                    </h3>
                    <ul class="space-y-2.5 text-sm text-gray-600">
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-500 mt-0.5 text-xs"></i> Include exact error messages, if any</li>
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-500 mt-0.5 text-xs"></i> List the steps that led to the issue</li>
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-500 mt-0.5 text-xs"></i> Attach a screenshot or screen recording</li>
                        <li class="flex gap-2"><i class="fas fa-check text-emerald-500 mt-0.5 text-xs"></i> Mention what you expected to happen instead</li>
                    </ul>
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6">
                    <h3 class="text-sm font-semibold text-blue-900 mb-1.5 flex items-center gap-2">
                        <i class="fas fa-shield-heart"></i> Need something urgent?
                    </h3>
                    <p class="text-xs text-blue-800/80 leading-relaxed">
                        For account or billing emergencies outside these hours, email
                        <a href="mailto:{{ $globalSettings->support_email ?? 'support@' . request()->getHost() }}" class="font-medium underline">{{ $globalSettings->support_email ?? 'support@' . request()->getHost() }}</a> directly.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        #dropzone.dragover {
            border-color: #3B82F6;
            background-color: rgba(59, 130, 246, 0.06);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Character counter
            const message = document.getElementById('message');
            const messageCount = document.getElementById('messageCount');
            function updateCount() {
                const len = message.value.length;
                messageCount.textContent = `${len} character${len === 1 ? '' : 's'}`;
            }
            message.addEventListener('input', updateCount);
            updateCount();

            // File upload: drag & drop, preview list, client-side validation
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('attachments');
            const fileList = document.getElementById('fileList');
            const fileErrors = document.getElementById('fileErrors');

            const MAX_FILES = 5;
            const MAX_SIZE = 10 * 1024 * 1024; // 10MB
            let currentFiles = [];

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
                const ext = name.split('.').pop().toLowerCase();
                return ICONS[ext] || 'fa-file text-gray-400';
            }

            function formatSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
            }

            function showErrors(messages) {
                if (!messages.length) {
                    fileErrors.classList.add('hidden');
                    fileErrors.innerHTML = '';
                    return;
                }
                fileErrors.classList.remove('hidden');
                fileErrors.innerHTML = messages.map(m => `<div><i class="fas fa-circle-exclamation mr-1"></i>${m}</div>`).join('');
            }

            function renderFileList() {
                fileList.innerHTML = '';
                currentFiles.forEach((file, index) => {
                    const row = document.createElement('div');
                    row.className = 'flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2';
                    row.innerHTML = `
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fas ${iconFor(file.name)}"></i>
                            <span class="text-sm text-gray-700 truncate">${file.name}</span>
                            <span class="text-xs text-gray-400 shrink-0">${formatSize(file.size)}</span>
                        </div>
                        <button type="button" data-index="${index}" class="remove-file text-gray-400 hover:text-red-500 shrink-0 ml-2">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    fileList.appendChild(row);
                });

                fileList.querySelectorAll('.remove-file').forEach(btn => {
                    btn.addEventListener('click', () => {
                        currentFiles.splice(parseInt(btn.dataset.index), 1);
                        syncInputFiles();
                        renderFileList();
                    });
                });
            }

            function syncInputFiles() {
                const dt = new DataTransfer();
                currentFiles.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
            }

            function addFiles(newFiles) {
                const errors = [];
                const incoming = Array.from(newFiles);

                for (const file of incoming) {
                    if (currentFiles.length >= MAX_FILES) {
                        errors.push(`Only up to ${MAX_FILES} files are allowed.`);
                        break;
                    }
                    if (file.size > MAX_SIZE) {
                        errors.push(`"${file.name}" is too large (max 10MB per file).`);
                        continue;
                    }
                    if (currentFiles.some(f => f.name === file.name && f.size === file.size)) {
                        continue; // skip exact duplicate
                    }
                    currentFiles.push(file);
                }

                showErrors(errors);
                syncInputFiles();
                renderFileList();
            }

            fileInput.addEventListener('change', (e) => {
                addFiles(e.target.files);
            });

            ['dragenter', 'dragover'].forEach(evt => {
                dropzone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    dropzone.classList.add('dragover');
                });
            });

            ['dragleave', 'drop'].forEach(evt => {
                dropzone.addEventListener(evt, (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('dragover');
                });
            });

            dropzone.addEventListener('drop', (e) => {
                if (e.dataTransfer?.files?.length) {
                    addFiles(e.dataTransfer.files);
                }
            });

            // Prevent double-submit
            const form = document.getElementById('ticketForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            form.addEventListener('submit', () => {
                submitBtn.disabled = true;
                submitBtnText.textContent = 'Submitting…';
            });
        });
    </script>
@endsection
