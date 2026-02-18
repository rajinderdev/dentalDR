<aside class="peer group fixed top-0 left-0 h-full w-16 hover:w-56 bg-blue-600 text-white flex flex-col z-30 transition-all duration-200 overflow-hidden">
    {{-- Logo --}}
    <div class="flex items-center gap-2 px-5 py-5">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
        </svg>
        <span class="hidden group-hover:inline text-lg font-bold tracking-wide">DentalDR</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-2 group-hover:px-3 py-2 space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
            <span class="hidden group-hover:inline">Dashboard</span>
        </a>

        <a href="#" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="hidden group-hover:inline">Front-Desk</span>
        </a>

        <a href="#" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span class="hidden group-hover:inline">Patient</span>
        </a>

        <a href="#" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span class="hidden group-hover:inline">Calendar</span>
        </a>

        <a href="#" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="hidden group-hover:inline">Appointments</span>
        </a>

        <a href="#" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
            <span class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <span class="hidden group-hover:inline">Lab Work</span>
            </span>
            <svg class="hidden group-hover:block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>

        <a href="#" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
            <span class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                <span class="hidden group-hover:inline">Communications</span>
            </span>
            <svg class="hidden group-hover:block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>

        <a href="#" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            <span class="hidden group-hover:inline">Documents</span>
        </a>

        <a href="#" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
            <span class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="hidden group-hover:inline">Reports</span>
            </span>
            <svg class="hidden group-hover:block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>

        @auth
            @php
                $sidebarIsAdmin = false;
                if (method_exists(auth()->user(), 'hasRole')) {
                    $sidebarIsAdmin = auth()->user()->hasRole('Administrator') || auth()->user()->hasRole('administrator');
                }
                if (! $sidebarIsAdmin) {
                    $roleName = auth()->user()->RoleName ?? null;
                    $sidebarIsAdmin = is_string($roleName) && strtolower($roleName) === 'administrator';
                }
            @endphp

            @if ($sidebarIsAdmin)
                <details class="group/admin">
                    <summary class="list-none flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 cursor-pointer">
                        <span class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-10 1.67-10 5v1h20v-1c0-3.33-6.67-5-10-5z"/></svg>
                            <span class="hidden group-hover:inline">Administrator</span>
                        </span>
                        <svg class="hidden group-hover:block w-4 h-4 transition-transform group-open/admin:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </summary>

                    <div class="hidden group-hover:block ml-9 space-y-1 pt-1">
                        <a href="{{ route('admin.roles.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.roles.*') ? 'bg-blue-700' : '' }}">Manage Roles</a>
                        <a href="{{ route('admin.users.create') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">Add User</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">Manage User</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">Manage Doctors</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">Manage Clinic</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">Manage Chairs</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">Manage Medicine</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">Manage Rx-Template</a>
                        <a href="#" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">Manage LookUps</a>
                    </div>
                </details>
            @endif
        @endauth

        <a href="#" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="hidden group-hover:inline">Doctor</span>
        </a>

        <a href="#" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
            <span class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="hidden group-hover:inline">Accounts</span>
            </span>
            <svg class="hidden group-hover:block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>

        <a href="#" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
            <span class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span class="hidden group-hover:inline">Inventory</span>
            </span>
            <svg class="hidden group-hover:block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </nav>

    {{-- Sign Out --}}
    <div class="px-3 py-4 border-t border-blue-500">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center justify-center group-hover:justify-start gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span class="hidden group-hover:inline">Sign Out</span>
            </button>
        </form>
    </div>
</aside>
