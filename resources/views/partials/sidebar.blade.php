<aside class="peer group fixed top-0 left-0 h-full w-16 hover:w-56 bg-blue-600 text-white flex flex-col z-30 transition-all duration-200 overflow-hidden">
    {{-- Logo --}}
    <div class="flex items-center gap-2 px-5 py-5">
        <a href="https://dental.stgserver.co.in/dashboard">
            <img src="{{asset('assets/images/logo/logo.png')}}">
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-2 group-hover:px-3 py-2 space-y-1">
        <a href="https://dental.stgserver.co.in/dashboard" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-500' }}">
            <img src="{{asset('assets/images/menu/dashboard-active.png')}}">
            <span class="hidden group-hover:inline">Dashboard</span>
        </a>

        <a href="https://dental.stgserver.co.in/front-desk" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
               <img src="{{asset('assets/images/menu/front-desk.png')}}">
            <span class="hidden group-hover:inline">Front-Desk</span>
        </a>

        <a href="https://dental.stgserver.co.in/patients" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
               <img src="{{asset('assets/images/menu/patient.png')}}">
            <span class="hidden group-hover:inline">Patient</span>
        </a>

        <a href="https://dental.stgserver.co.in/calender" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                <img src="{{asset('assets/images/menu/appointment.png')}}">
            <span class="hidden group-hover:inline">Calendar</span>
        </a>

        <a href="https://dental.stgserver.co.in/appointment" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                <img src="{{asset('assets/images/menu/appointment.png')}}">
                 <span class="hidden group-hover:inline">Appointments</span>
        </a>

        <details class="group/admin">
            <summary class="list-none flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 cursor-pointer">
                <span class="flex items-center gap-3">
                    <img src="{{asset('assets/images/menu/labwork-active.png')}}" alt="Lab work" class="w-5 h-5">
                    <span class="hidden group-hover:inline">Lab work</span>
                </span>
                <svg class="hidden group-hover:block w-4 h-4 transition-transform group-open/admin:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </summary>

            <div class="hidden group-hover:block ml-9 space-y-1 pt-1">
                <a href="https://dental.stgserver.co.in/manage/Lab-Work" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <span class="flex items-center gap-3">
                        <img src="{{asset('assets/images/menu/manage-lab-work.png')}}">
                        <span class="hidden group-hover:inline">Manage Lab Work</span>
                    </span>
                </a>
                <a href="https://dental.stgserver.co.in/manage/Suppliers" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <span class="flex items-center gap-3">
                        <img src="{{asset('assets/images/menu/suppliers.png')}}">
                        <span class="hidden group-hover:inline">Manage Suppliers</span>
                    </span>
                </a>
                <a href="https://dental.stgserver.co.in/manage/lab-Lookups" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <span class="flex items-center gap-3">
                        <img src="h{{asset('assets/images/menu/lab-lookups.png')}}">
                        <span class="hidden group-hover:inline">Manage lab Lookups</span>
                    </span>
                </a>
                <a href="https://dental.stgserver.co.in/manage/lab-Items" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <span class="flex items-center gap-3">
                        <img src="{{asset('assets/images/menu/lab-items.png')}}">
                        <span class="hidden group-hover:inline">Manage lab Items</span>
                    </span>
                </a>
                    
            </div>
        </details>

       
        <details class="group/admin">
            <summary class="list-none flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 cursor-pointer">
                <span class="flex items-center gap-3">
                    <img src="{{asset('assets/images/menu/appointment-active.png')}}" alt="Lab work" class="w-5 h-5">
                    <span class="hidden group-hover:inline">Communications</span>
                </span>
                <svg class="hidden group-hover:block w-4 h-4 transition-transform group-open/admin:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </summary>

            <div class="hidden group-hover:block ml-9 space-y-1 pt-1">
                <a href="https://dental.stgserver.co.in/communications/sms" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <span class="flex items-center gap-3">
                        <img src="{{asset('assets/images/menu/appointment-active.png')}}">
                        <span class="hidden group-hover:inline">SMS</span>
                    </span>
                </a>
                <a href="https://dental.stgserver.co.in/communications/sms-templates" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <span class="flex items-center gap-3">
                        <img src="{{asset('assets/images/menu/appointment-active.png')}}">
                        <span class="hidden group-hover:inline">SMS Templates</span>
                    </span>
                </a>
                <a href="https://dental.stgserver.co.in/communications/email" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <span class="flex items-center gap-3">
                        <img src="{{asset('assets/images/menu/appointment-active.png')}}">
                        <span class="hidden group-hover:inline">Email</span>
                    </span>
                </a>
                <a href="https://dental.stgserver.co.in/communications/birthday-reminder" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <span class="flex items-center gap-3">
                        <img src="{{asset('assets/images/menu/appointment-active.png')}}">
                        <span class="hidden group-hover:inline">Birthday Reminder</span>
                    </span>
                </a>
                <a href="https://dental.stgserver.co.in/communications/personal-reminder" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <span class="flex items-center gap-3">
                        <img src="{{asset('assets/images/menu/appointment-active.png')}}">
                        <span class="hidden group-hover:inline">Personal Reminder</span>
                    </span>
                </a>
                <a href="https://dental.stgserver.co.in/communications/checkup-reminder" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <span class="flex items-center gap-3">
                        <img src="{{asset('assets/images/menu/appointment-active.png')}}">
                        <span class="hidden group-hover:inline">CheckUp Reminder</span>
                    </span>
                </a>
                <a href="https://dental.stgserver.co.in/communications/pateint-reminder" class="flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <span class="flex items-center gap-3">
                        <img src="{{asset('assets/images/menu/appointment-active.png')}}">
                        <span class="hidden group-hover:inline">Pateint Reminder</span>
                    </span>
                </a>
                        
            </div>
        </details>

        <a href="https://dental.stgserver.co.in/documents" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
               <img src="{{asset('assets/images/menu/appointment.png')}}">
               <span class="hidden group-hover:inline">Documents</span>
        </a>

        <details class="group">
            <summary class="list-none flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 cursor-pointer">
                <span class="flex items-center gap-3">
                    <img src="{{asset('assets/images/menu/dashboard-active.png')}}">
                    <span class="hidden group-hover:inline">Reports</span>
                </span>
                <svg class="hidden group-hover:block w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </summary>

            <div class="hidden group-hover:block ml-9 space-y-1 pt-1">
                <a href="https://dental.stgserver.co.in/reports/billing-report" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Billing Report</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/patient-report-details" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Daily Patient Attendance</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/daily-collection" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Daily Collection</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/patient-list" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Daily New Patient</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/doctor-incentive-report" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Doctor Incentive Report</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/doctor-wise-report" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Doctor Wise Report</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/sms-report" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Milling List</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/outstanding-bills" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Outstandings Bills</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/patient-morbidity-details" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Patient Morbidity Detail</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/patients-report" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Patients Report</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/refferal-report" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Referral Report</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/sms-report" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">SMS Report</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/waiting-area-report" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Waiting Area Report</span>
                </a>
                <a href="https://dental.stgserver.co.in/reports/work-report" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Work Report</span>
                </a>
            </div>
        </details>

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
                        <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">Manage User</a>
                        <a href="{{ route('admin.doctors.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.doctors.*') ? 'bg-blue-700' : '' }}">Manage Doctors</a>
                        <a href="{{ route('admin.clinic.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.clinic.*') ? 'bg-blue-700' : '' }}">Manage Clinic</a>
                        <a href="{{ route('admin.chairs.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.chairs.*') ? 'bg-blue-700' : '' }}">Manage Chairs</a>
                        <a href="{{ route('admin.medicines.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.medicines.*') ? 'bg-blue-700' : '' }}">Manage Medicine</a>
                        <a href="{{ route('admin.rx-templates.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.rx-templates.*') ? 'bg-blue-700' : '' }}">Manage Rx-Template</a>
                        <a href="{{ route('admin.lookups.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.lookups.*') ? 'bg-blue-700' : '' }}">Manage LookUps</a>
                        <a href="{{ route('admin.bank-accounts.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.bank-accounts.*') ? 'bg-blue-700' : '' }}">Manage Bank Acc</a>
                        <a href="{{ route('admin.doc-types.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.doc-types.*') ? 'bg-blue-700' : '' }}">Manage DocType</a>
                        <a href="{{ route('admin.clinic-attributes.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.clinic-attributes.*') ? 'bg-blue-700' : '' }}">Manage Clinic Attributes</a>
                        <a href="{{ route('admin.treatment-hierarchy.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.treatment-hierarchy.*') ? 'bg-blue-700' : '' }}">Treatment Hierarchy</a>
                        <a href="{{ route('admin.communication-attributes.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.communication-attributes.*') ? 'bg-blue-700' : '' }}">Manage Communication A...</a>
                        <a href="{{ route('admin.lab-items.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.lab-items.*') ? 'bg-blue-700' : '' }}">Manage Lab Items</a>
                        <a href="{{ route('admin.backup-database.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.backup-database.*') ? 'bg-blue-700' : '' }}">Backup Database</a>
                        <a href="{{ route('admin.user-activities.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 {{ request()->routeIs('admin.user-activities.*') ? 'bg-blue-700' : '' }}">Users Activities</a>
                    </div>
                </details>
            @endif
        @endauth

        <a href="https://dental.stgserver.co.in/doctors" class="flex items-center justify-center group-hover:justify-start gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
               <img src="{{asset('assets/images/menu/document.png')}}">
               <span class="hidden group-hover:inline">Doctor</span>
        </a>

        <details class="group">
            <summary class="list-none flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 cursor-pointer">
                <span class="flex items-center gap-3">
                    <img src="{{asset('assets/images/menu/account-active.png')}}">
                    <span class="hidden group-hover:inline">Accounts</span>
                </span>
                <svg class="hidden group-hover:block w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </summary>

            <div class="hidden group-hover:block ml-9 space-y-1 pt-1">
                <a href="https://dental.stgserver.co.in/account/invoice" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Invoices</span>
                </a>
                <a href="https://dental.stgserver.co.in/account/receipts" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Recipts</span>
                </a>
                <a href="https://dental.stgserver.co.in/account/passbook" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Passbook</span>
                </a>
                <a href="https://dental.stgserver.co.in/account/doctor-incentive" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Doctor Incentive</span>
                </a>
                <a href="https://dental.stgserver.co.in/account/petty-cash" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Petty Cash</span>
                </a>
            </div>
        </details>
  <details class="group">
            <summary class="list-none flex items-center justify-center group-hover:justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500 cursor-pointer">
                <span class="flex items-center gap-3">
                    <img src="{{asset('assets/images/menu/inventory-active.png')}}">
                    <span class="hidden group-hover:inline">Inventory</span>
                </span>
                <svg class="hidden group-hover:block w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </summary>

            <div class="hidden group-hover:block ml-9 space-y-1 pt-1">
                <a href="https://dental.stgserver.co.in/inventory/item-master" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Item Master</span>
                </a>
                <a href="https://dental.stgserver.co.in/inventory/item-hierarchy" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Item hierarchy</span>
                </a>
                <a href="https://dental.stgserver.co.in/inventory/item-supplier" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Item Supplier</span>
                </a>
                <a href="https://dental.stgserver.co.in/inventory/purchase-order" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Purchase Order</span>
                </a>
                <a href="https://dental.stgserver.co.in/inventory/item-customer" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Item Customer</span>
                </a>
                <a href="https://dental.stgserver.co.in/inventory/sales-order" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Sales Order</span>
                </a>
                <a href="https://dental.stgserver.co.in/inventory/activity" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Activity</span>
                </a>
                <a href="https://dental.stgserver.co.in/inventory/stock" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Stock</span>
                </a>
                <a href="https://dental.stgserver.co.in/inventory/designations" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Designations</span>
                </a>
                <a href="https://dental.stgserver.co.in/inventory/buildings" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Buildings</span>
                </a>
                <a href="https://dental.stgserver.co.in/inventory/patient-habits" class="block px-3 py-2 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/manage-lab-work.png')}}"><span class="hidden group-hover:inline">Patient Habits</span>
                </a>
                
            </div>
        </details>
    </nav>

    {{-- Sign Out --}}
    <div class="px-3 py-4 border-t border-blue-500">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center justify-center group-hover:justify-start gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-blue-100 hover:bg-blue-500">
                    <img src="{{asset('assets/images/menu/dashboard-active.png')}}">
                    <span class="hidden group-hover:inline">Sign Out</span>
            </button>
        </form>
    </div>
</aside>
