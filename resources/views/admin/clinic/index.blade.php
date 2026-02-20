@extends('layouts.admin')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-4 md:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-4">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Manage Clinics</h1>
        <a href="{{ route('admin.clinic.create') }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Clinic
        </a>
    </div>

    <!-- Search -->
    <div class="mb-6 flex flex-wrap gap-4 items-center">
        <input type="text" id="searchInput" placeholder="Search clinics..."
               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <a href="{{ route('admin.clinic.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            Clear Filters
        </a>
    </div>

    <!-- Clinics Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="clinic-table">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Sr No.</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Clinic Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">City</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">State</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Country</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Created On</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('page_js')
<script>
$(document).ready(function() {
    var table = $('#clinic-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('admin.clinic.data') }}",
            data: function(d) {
                d.search = $('#searchInput').val();
            }
        },
        columns: [
            {
                data: null,
                name: 'serial',
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'Name',
                name: 'Name',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'Email',
                name: 'Email',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'Phone',
                name: 'Phone',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'City',
                name: 'City',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'State',
                name: 'State',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'Country',
                name: 'CountryID',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            { 
                data: 'CreatedOn',
                name: 'CreatedOn',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-right'
            },
        ],
        order: [[1, 'asc']],
        language: {
            processing: '<div class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Processing...</span></div>',
            zeroRecords: 'No clinics found',
            info: 'Showing _START_ to _END_ of _TOTAL_ clinics',
            infoEmpty: 'No clinics available',
            infoFiltered: '(filtered from _MAX_ total clinics)',
            lengthMenu: 'Show _MENU_ clinics',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        },
        responsive: true
    });

    // Custom search handler
    $('#searchInput').on('keyup', function() {
        table.draw();
    });
});

function deleteClinic(clinicId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This clinic will be removed from the system!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.clinic.destroy", ":clinicId") }}'.replace(':clinicId', clinicId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', 'Clinic has been deleted.', 'success').then(() => {
                            $('#clinic-table').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Error deleting clinic.', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error deleting clinic';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
}
</script>
@endsection
