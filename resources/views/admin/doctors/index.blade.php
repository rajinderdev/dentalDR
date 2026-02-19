@extends('layouts.admin')
@push('styles')
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Doctors</h1>
        <a href="{{ route('admin.doctors.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-1"></i> Add Doctor
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 flex flex-wrap gap-4 items-center">
        <input type="text" id="searchInput" placeholder="Search doctors..."
               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select id="categoryFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Categories</option>
            <option value="General">General</option>
            <option value="Orthodontist">Orthodontist</option>
            <option value="Endodontist">Endodontist</option>
            <option value="Periodontist">Periodontist</option>
            <option value="Prosthodontist">Prosthodontist</option>
            <option value="Oral Surgeon">Oral Surgeon</option>
            <option value="Pediatric">Pediatric</option>
        </select>
        <a href="{{ route('admin.doctors.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Clear Filters
        </a>
    </div>

    <!-- Doctors Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="doctor-table">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Sr No.</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Reg. No.</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Incentive</th>
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
    var table = $('#doctor-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('admin.doctors.index') }}",
            data: function(d) {
                d.search = $('#searchInput').val();
                d.category = $('#categoryFilter').val();
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
                data: 'ProviderName',
                name: 'ProviderName',
                orderable: true
            },
            {
                data: 'PhoneNumber',
                name: 'PhoneNumber',
                orderable: false,
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
                data: 'Category',
                name: 'Category',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'RegistrationNumber',
                name: 'RegistrationNumber',
                orderable: false,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'IncentiveValue',
                name: 'IncentiveValue',
                orderable: false
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-right'
            },
        ],
        order: [[1, 'asc']],
        language: {
            processing: '<div class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Processing...</span></div>',
            zeroRecords: 'No doctors found',
            info: 'Showing _START_ to _END_ of _TOTAL_ doctors',
            infoEmpty: 'No doctors available',
            infoFiltered: '(filtered from _MAX_ total doctors)',
            lengthMenu: 'Show _MENU_ doctors',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        },
        responsive: true
    });

    // Custom search and filter handlers
    $('#searchInput').on('keyup', function() {
        table.draw();
    });

    $('#categoryFilter').on('change', function() {
        table.draw();
    });
});

function deleteDoctor(doctorId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This doctor will be removed from the system!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.doctors.destroy", ":doctorId") }}'.replace(':doctorId', doctorId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', 'Doctor has been deleted.', 'success').then(() => {
                            $('#doctor-table').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Error deleting doctor.', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error deleting doctor';
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
