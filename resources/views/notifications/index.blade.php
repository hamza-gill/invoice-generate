@extends('layouts.auth.app')

@section('title', 'Notifications - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">All Notifications</h1>

        <form action="{{ route('notifications.markAllRead') }}" method="POST">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-check mr-2"></i> Mark All as Read
            </button>
        </form>
    </div>

    <div class="bg-white shadow rounded-xl border border-gray-100 overflow-hidden">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-50 text-left text-gray-600 uppercase text-sm">
            <tr>
                <th class="p-4 border-b">Title</th>
                <th class="p-4 border-b">Message</th>
                <th class="p-4 border-b">Status</th>
                <th class="p-4 border-b">Date</th>
                <th class="p-4 border-b text-right">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($notifications as $notification)
                @php $data = $notification->data; @endphp
                <tr class="hover:bg-gray-50 transition {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                    <td class="p-4 border-b font-semibold">{{ $data['title'] ?? 'Notification' }}</td>
                    <td class="p-4 border-b text-gray-700">{{ $data['message'] ?? '' }}</td>
                    <td class="p-4 border-b">
                        @if(isset($notification['status']))
                            <span class="px-2 py-1 rounded-full text-xs uppercase
                                @switch($notification['status'])
                                    @case('accepted') bg-green-100 text-green-700 @break
                                    @case('paid') bg-green-100 text-green-700 @break
                                    @case('declined') bg-red-100 text-red-700 @break
                                    @case('rejected') bg-red-100 text-red-700 @break
                                    @case('revisited') bg-blue-100 text-blue-700 @break
                                    @case('viewed') bg-gray-100 text-gray-600 @break
                                    @case('alert') bg-yellow-100 text-yellow-700 @break
                                    @case('invalid_action') bg-orange-100 text-orange-700 @break
                                    @case('complete') bg-green-100 text-green-700 @break
                                    @case('incomplete') bg-yellow-100 text-yellow-700 @break
                                    @default bg-gray-100 text-gray-600
                                @endswitch">
                                {{ ucfirst($notification['status']) }}
                             </span>
                        @endif

                    </td>
                    <td class="p-4 border-b text-gray-500">{{ $notification->created_at->format('M d, Y h:i A') }}</td>
                    <td class="p-4 border-b text-right">
                        <a href="{{ $data['redirect_url'] ?? '#' }}" target="_blank"
                           class="text-blue-600 hover:underline text-sm">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-500">No notifications found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8 flex justify-center">
        {{ $notifications->links('components.pagination') }}
    </div>
@endsection
