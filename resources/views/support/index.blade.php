@extends('layouts.auth.app')

@section('title', 'Support Tickets - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Support Tickets</h1>
        <a href="{{ route('support.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-flex items-center">
            <i class="fas fa-plus mr-2"></i> New Ticket
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded-lg">{{ session('success') }}</div>
    @endif

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

    <div class="bg-white shadow rounded-xl border border-gray-100 overflow-hidden">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-50 text-left text-gray-600 uppercase text-sm">
            <tr>
                <th class="p-4 border-b">Subject</th>
                <th class="p-4 border-b">Priority</th>
                <th class="p-4 border-b">Status</th>
                <th class="p-4 border-b">Last Activity</th>
                <th class="p-4 border-b text-right">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-50 transition {{ $ticket->is_read_by_org ? '' : 'bg-orange-50' }}">
                    <td class="p-4 border-b font-semibold text-gray-800">
                        {{ $ticket->subject }}
                        @if(! $ticket->is_read_by_org)
                            <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-orange-100 text-orange-700">New</span>
                        @endif
                    </td>
                    <td class="p-4 border-b">
                        <span class="px-2 py-1 rounded-full text-xs capitalize {{ $priorityColors[$ticket->priority] ?? 'bg-gray-100 text-gray-700' }}">{{ $ticket->priority }}</span>
                    </td>
                    <td class="p-4 border-b">
                        <span class="px-2 py-1 rounded-full text-xs capitalize {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-600' }}">{{ str_replace('_', ' ', $ticket->status) }}</span>
                    </td>
                    <td class="p-4 border-b text-gray-500">{{ optional($ticket->last_message_at)->format('M d, g:i A') ?? '—' }}</td>
                    <td class="p-4 border-b text-right">
                        <a href="{{ route('support.show', $ticket) }}" class="text-blue-600 hover:underline text-sm">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-500">
                        No support tickets yet.
                        <a href="{{ route('support.create') }}" class="text-blue-600 hover:underline">Create one</a>.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
