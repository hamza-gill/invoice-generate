@extends('layouts.auth.app')

@section('title', 'My Account - '.($globalSettings->company_name ?? config('app.name')))

@section('content')
    <header class="bg-white border-b border-gray-200 px-8 py-4 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">My Account</h2>
    </header>

    <main class="p-8 space-y-8">

        <!-- Profile Card -->
        <div class="bg-white rounded-xl shadow border border-gray-100 p-6">
            <div class="flex items-start space-x-6">
                <div class="w-20 h-20 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-white text-2xl"></i>
                </div>
                <div class="flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-500 text-xs uppercase tracking-wide mb-1">First Name</label>
                            <p class="text-gray-800 font-semibold">{{ $user->first_name ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-xs uppercase tracking-wide mb-1">Last Name</label>
                            <p class="text-gray-800 font-semibold">{{ $user->last_name ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-xs uppercase tracking-wide mb-1">Email</label>
                            <p class="text-gray-800 font-semibold">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-xs uppercase tracking-wide mb-1">Phone</label>
                            <p class="text-gray-800 font-semibold">{{ $user->phone ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-xs uppercase tracking-wide mb-1">Role</label>
                            <p class="text-gray-800 font-semibold">
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 text-xs rounded-full">
                                    {{ ucfirst($user->role ?? 'User') }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-xs uppercase tracking-wide mb-1">Status</label>
                            <p class="text-gray-800 font-semibold">
                                @if($user->is_active)
                                    <span class="bg-green-100 text-green-700 px-2 py-1 text-xs rounded-full">Active</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 text-xs rounded-full">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-xs uppercase tracking-wide mb-1">Member Since</label>
                            <p class="text-gray-800 font-semibold">{{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-xs uppercase tracking-wide mb-1">Organization</label>
                            <p class="text-gray-800 font-semibold">{{ $user->organization->name ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Stats -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                <p class="text-gray-500 text-sm">My Invoices</p>
                <h3 class="text-2xl font-bold text-gray-800">{{ $totalInvoices }}</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                <p class="text-gray-500 text-sm">Paid Invoices</p>
                <h3 class="text-2xl font-bold text-green-600">{{ $paidInvoices }}</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                <p class="text-gray-500 text-sm">Total Revenue</p>
                <h3 class="text-2xl font-bold text-blue-600">${{ number_format($totalRevenue, 2) }}</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                <p class="text-gray-500 text-sm">Org Customers</p>
                <h3 class="text-2xl font-bold text-purple-600">{{ $totalCustomers }}</h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
                <p class="text-gray-500 text-sm">Org Products</p>
                <h3 class="text-2xl font-bold text-orange-600">{{ $totalProducts }}</h3>
            </div>
        </div>

    </main>
@endsection