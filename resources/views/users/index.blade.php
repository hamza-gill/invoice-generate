@extends('layouts.auth.app')

@section('title', 'User Management - '.($globalSettings->company_name ?? config('app.name')))

@section('content')
    <header class="bg-white border-b border-gray-200 px-8 py-4 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">User Management</h2>
        <button id="inviteUserBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-user-plus mr-2"></i>Invite User
        </button>
    </header>

    <main class="p-8">
        <div class="bg-white p-6 rounded-xl shadow border border-gray-100">
            <table class="min-w-full text-sm text-gray-700">
                <thead>
                <tr class="border-b">
                    <th class="text-left py-3">First Name</th>
                    <th class="text-left py-3">Last Name</th>
                    <th class="text-left py-3">Email</th>
                    <th class="text-left py-3">Role</th>
                    <th class="text-left py-3">Status</th>
                    <th class="text-right py-3">Actions</th>
                </tr>
                </thead>

                <tbody>
                @foreach($users as $user)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3">{{ $user->first_name ?? '—' }}</td>
                        <td class="py-3">{{ $user->last_name ?? '—' }}</td>
                        <td class="py-3">{{ $user->email }}</td>

                        <td class="py-3">
                            <select data-id="{{ $user->id }}" class="roleSelect border rounded p-1 text-sm">
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td class="py-3">
                            @if($user->status == 'active')
                                <span class="bg-green-100 text-green-700 px-2 py-1 text-xs rounded">Active</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 text-xs rounded">Pending</span>
                            @endif
                        </td>

                        <td class="py-3 text-right space-x-3">
                            <button class="text-blue-600 hover:text-blue-800 text-sm">View</button>

                            <!-- Revoke Button -->
                            <button class="text-red-600 hover:text-red-800 text-sm revokeBtn"
                                    data-id="{{ $user->id }}">
                                Revoke
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </main>

    <div class="mt-8 flex justify-center">
        {{ $users->links('components.pagination') }}
    </div>

    <!-- Invite Modal -->
    <div id="inviteModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50">
        <div class="bg-white p-6 rounded-xl w-96 shadow-lg">
            <h3 class="text-lg font-bold mb-4">Invite New User</h3>

            <form id="inviteForm">
                @csrf

                <label class="block text-sm mb-2">First Name</label>
                <input type="text" name="first_name" required class="w-full border rounded p-2 mb-4">

                <label class="block text-sm mb-2">Last Name</label>
                <input type="text" name="last_name" required class="w-full border rounded p-2 mb-4">

                <label class="block text-sm mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full border rounded p-2 mb-4">

                <label class="block text-sm mb-2">Role</label>
                <select name="role" required class="w-full border rounded p-2 mb-4">
                    @foreach($roles as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>

                <div class="flex justify-end space-x-3">
                    <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Send Invite</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Open/Close modal
        document.getElementById('inviteUserBtn').onclick = () =>
            document.getElementById('inviteModal').classList.remove('hidden');

        document.getElementById('closeModalBtn').onclick = () =>
            document.getElementById('inviteModal').classList.add('hidden');

        // Submit Invite Form
        document.getElementById('inviteForm').onsubmit = function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('{{ route("users.invite") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        icon: data.success ? 'success' : 'error',
                        title: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    if (data.success) location.reload();
                });
        };

        // Update Role
        document.querySelectorAll('.roleSelect').forEach(select => {
            select.addEventListener('change', e => {
                const id = e.target.dataset.id;
                const role = e.target.value;

                fetch(`/users/${id}/role`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ role })
                })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.success ? 'success' : 'error',
                            title: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    });
            });
        });

        // Revoke User
        document.querySelectorAll('.revokeBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const userId = btn.dataset.id;

                Swal.fire({
                    title: "Are you sure?",
                    text: "This will deactivate the user!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, revoke"
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch(`/users/${userId}/revoke`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(res => res.json())
                            .then(data => {
                                Swal.fire({
                                    icon: data.success ? 'success' : 'error',
                                    title: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                if (data.success) location.reload();
                            });
                    }
                });
            });
        });

    </script>

@endsection
