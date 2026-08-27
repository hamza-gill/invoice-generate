<aside class="w-56 bg-sidebar text-white flex flex-col h-screen sticky top-0">
    <!-- 🌐 Logo Section -->
    @php
        $companyName = $globalSettings->company_name ?? config('app.name');
        $nameLen = mb_strlen($companyName);

        // 4-tier dynamic font sizing based on name length
        // (full sidebar width is now available to the name, so thresholds are more generous)
        if ($nameLen <= 15) {
            $nameFont = 'text-lg';      // short names — biggest
        } elseif ($nameLen <= 25) {
            $nameFont = 'text-base';    // medium
        } elseif ($nameLen <= 40) {
            $nameFont = 'text-sm';      // long
        } else {
            $nameFont = 'text-xs';      // very long — smallest
        }
    @endphp
    <div class="p-6 border-b border-gray-700">
        <div class="flex flex-col items-center text-center space-y-2">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exchange-alt text-white"></i>
            </div>

            <div class="w-full">
                <h1 class="{{ $nameFont }} font-bold leading-snug line-clamp-2 break-words" title="{{ $companyName }}">
                    {{ $companyName }}
                </h1>
                <p class="text-[11px] text-gray-400 mt-0.5">Invoice Reconciliation</p>
            </div>
        </div>

    </div>

    <!-- 📋 Navigation Menu -->
    <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
        <!-- Dashboard - Available to all authenticated users -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                  {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
            <i class="fas fa-th-large w-5"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        @if(auth()->user()->isAdmin() || auth()->user()->isOrganizationOwner())
            <a href="{{ route('subscription.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                      {{ request()->routeIs('subscription.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
                <i class="fas fa-crown w-5"></i>
                <span>Subscription</span>
            </a>
        @endif

        <!-- Invoices - Admin, Developer, Manager, Employee -->
        @if(in_array(auth()->user()->role, ['admin', 'developer', 'manager', 'employee']))
            <a href="{{ route('invoices.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                      {{ request()->routeIs('invoices.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
                <i class="fas fa-file-invoice w-5"></i>
                <span>Invoices</span>
            </a>
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'developer', 'manager', 'employee']))
            <a href="{{ route('recurring.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                      {{ request()->routeIs('recurring.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
                <i class="fas fa-sync w-5"></i>
                <span>Recurring</span>
            </a>
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'developer', 'manager', 'employee']))
            <a href="{{ route('estimates.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                      {{ request()->routeIs('estimates.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
                <i class="fas fa-file-contract w-5"></i>
                <span>Estimates</span>
            </a>
        @endif

        <!-- Customers - Admin, Developer, Manager, Employee -->
        @if(in_array(auth()->user()->role, ['admin', 'developer', 'manager', 'employee']))
            <a href="{{ route('customers.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                      {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
                <i class="fas fa-user w-5"></i>
                <span>Customers</span>
            </a>
        @endif

        <!-- Products - Admin, Developer, Manager, Employee -->
        @if(in_array(auth()->user()->role, ['admin', 'developer', 'manager', 'employee']))
            <a href="{{ route('products.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                      {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
                <i class="fas fa-box w-5"></i>
                <span>Products</span>
            </a>
        @endif

        <!-- Reports - Admin, Developer, Manager -->
        @if(in_array(auth()->user()->role, ['admin', 'developer', 'manager']))
            <a href="{{ route('reports') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                      {{ request()->routeIs('reports') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
                <i class="fas fa-chart-bar w-5"></i>
                <span>Reports</span>
            </a>
        @endif

        <!-- Users (Team Management) - Admin, Developer only -->
        @if(in_array(auth()->user()->role, ['admin', 'developer']))
            <a href="{{ route('users.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                      {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
                <i class="fas fa-users w-5"></i>
                <span>Users</span>
            </a>
        @endif

        <!-- Notifications - Available to all authenticated users -->
        <a href="{{ route('notifications.index') }}"
           class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                  {{ request()->routeIs('notifications.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
            <i class="fas fa-bell w-5"></i>
            <span>Notifications</span>
        </a>

        @if(in_array(auth()->user()->role, ['admin', 'developer', 'manager']))
            <a href="{{ route('templates.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                      {{ request()->routeIs('templates.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
                <i class="fas fa-palette w-5"></i>
                <span>Templates</span>
            </a>
        @endif

        <!-- Settings - Admin, Developer, Manager -->
        @if(in_array(auth()->user()->role, ['admin', 'developer', 'manager']))
            <a href="{{ route('settings.index') }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                      {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
                <i class="fas fa-cog w-5"></i>
                <span>Settings</span>
            </a>
        @endif
    </nav>

    <!-- 💬 Help & Support - Available to all authenticated users -->
    <div class="p-3 border-t border-gray-700 flex-shrink-0">
        <a href="{{ route('help') }}"
           class="flex items-center space-x-3 px-4 py-3 rounded-lg transition
                  {{ request()->routeIs('help') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-sidebar-hover' }}">
            <i class="fas fa-question-circle w-5"></i>
            <span>Help & Support</span>
        </a>
    </div>
</aside>
