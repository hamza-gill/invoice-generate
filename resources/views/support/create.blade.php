@extends('layouts.auth.app')

@section('title', 'New Support Ticket - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">New Support Ticket</h1>
        <a href="{{ route('support.index') }}" class="text-blue-600 hover:underline text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to tickets
        </a>
    </div>

    <div class="bg-white shadow rounded-xl border border-gray-100 p-6 max-w-2xl">
        @if($errors->any())
            <div class="mb-4 bg-red-100 text-red-800 px-4 py-2 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('support.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                       class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       placeholder="Briefly describe the issue">
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select id="priority" name="priority" required class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Use <span class="font-semibold text-red-600">Urgent</span> if this is blocking your work right now.</p>
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea id="message" name="message" rows="6" required
                          class="w-full border rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                          placeholder="Describe the issue in detail, including any steps to reproduce.">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 inline-flex items-center">
                <i class="fas fa-paper-plane mr-2"></i> Submit Ticket
            </button>
        </form>
    </div>
@endsection
