@extends('layouts.admin')
@push('styles')
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Chairs</h1>
        <a href="{{ route('admin.chairs.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-1"></i> Add Chair
        </a>
    </div>

    <!-- Search -->
    <div class="mb-6 flex flex-wrap gap-4 items-center">
        <input type="text" id="searchInput" placeholder="Search chairs..."
               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <a href="{{ route('admin.chairs.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Clear Filters
        </a>
    </div>

    <!-- Chairs Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="chair-table">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Sr No.</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Chair Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Start Time</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">End Time</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Time Interval</th>
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
    var table = $('#chair-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('admin.chairs.index') }}",
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
                data: 'Title',
                name: 'Title',
                orderable: true,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'Description',
                name: 'Description',
                orderable: false,
                render: function(data) {
                    return data || 'N/A';
                }
            },
            {
                data: 'StartTime',
                name: 'StartTime',
                orderable: false
            },
            {
                data: 'EndTime',
                name: 'EndTime',
                orderable: false
            },
            {
                data: 'TimeInterval',
                name: 'TimeInterval',
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
            zeroRecords: 'No chairs found',
            info: 'Showing _START_ to _END_ of _TOTAL_ chairs',
            infoEmpty: 'No chairs available',
            infoFiltered: '(filtered from _MAX_ total chairs)',
            lengthMenu: 'Show _MENU_ chairs',
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

function deleteChair(chairId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This chair will be removed from the system!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.chairs.destroy", ":chairId") }}'.replace(':chairId', chairId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', 'Chair has been deleted.', 'success').then(() => {
                            $('#chair-table').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Error deleting chair.', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error deleting chair';
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
