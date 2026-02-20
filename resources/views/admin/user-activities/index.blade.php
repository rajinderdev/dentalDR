@extends('layouts.admin')
@push('styles')
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Users Activities</h1>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-wrap gap-4 items-end">
        {{-- Search --}}
        <div>
            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" id="searchInput" placeholder="Search..."
                   class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-56 text-sm">
        </div>

        {{-- Date Range Checkbox --}}
        <div class="flex items-center gap-2 pb-2">
            <input type="checkbox" id="dateFilterCheck" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
            <label for="dateFilterCheck" class="text-sm font-medium text-gray-700 cursor-pointer">Search between two dates:</label>
        </div>

        {{-- Start Date --}}
        <div>
            <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start</label>
            <input type="date" id="startDate"
                   class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm date-filter-input" disabled>
        </div>

        {{-- End Date --}}
        <div>
            <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End</label>
            <input type="date" id="endDate"
                   class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm date-filter-input" disabled>
        </div>

        {{-- Search Button --}}
        <div class="pb-0">
            <button type="button" id="searchBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                <i class="fas fa-search mr-1"></i> Search
            </button>
        </div>

        {{-- Clear Button --}}
        <div class="pb-0">
            <button type="button" id="clearBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Activities Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="activities-table">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Created On</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Patient Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">UserName</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">EventTypeName</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">EventDetails</th>
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
    // Set default dates
    var today = new Date();
    var threeMonthsAgo = new Date();
    threeMonthsAgo.setMonth(threeMonthsAgo.getMonth() - 3);
    $('#endDate').val(today.toISOString().split('T')[0]);
    $('#startDate').val(threeMonthsAgo.toISOString().split('T')[0]);

    var table = $('#activities-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('admin.user-activities.index') }}",
            data: function(d) {
                d.search = $('#searchInput').val();
                d.date_filter = $('#dateFilterCheck').is(':checked') ? '1' : '0';
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
            }
        },
        columns: [
            {
                data: 'CreatedOn',
                name: 'CreatedOn',
                orderable: true,
                width: '160px'
            },
            {
                data: 'PatientName',
                name: 'PatientName',
                orderable: false
            },
            {
                data: 'CreatedBy',
                name: 'CreatedBy',
                orderable: true
            },
            {
                data: 'EventTypeName',
                name: 'EventTypeName',
                orderable: true
            },
            {
                data: 'EventDetails',
                name: 'EventDetails',
                orderable: false
            },
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            processing: '<div class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Processing...</span></div>',
            zeroRecords: 'No activities found',
            info: 'Showing _START_ to _END_ of _TOTAL_ activities',
            infoEmpty: 'No activities available',
            infoFiltered: '(filtered from _MAX_ total activities)',
            lengthMenu: 'Records: _MENU_',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        },
        responsive: true
    });

    // Toggle date filter inputs
    $('#dateFilterCheck').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.date-filter-input').prop('disabled', !isChecked);
        if (!isChecked) {
            table.draw();
        }
    });

    // Search button click
    $('#searchBtn').on('click', function() {
        table.draw();
    });

    // Enter key on search input
    $('#searchInput').on('keyup', function(e) {
        if (e.key === 'Enter') {
            table.draw();
        }
    });

    // Clear filters
    $('#clearBtn').on('click', function() {
        $('#searchInput').val('');
        $('#dateFilterCheck').prop('checked', false);
        $('.date-filter-input').prop('disabled', true);

        var today = new Date();
        var threeMonthsAgo = new Date();
        threeMonthsAgo.setMonth(threeMonthsAgo.getMonth() - 3);
        $('#endDate').val(today.toISOString().split('T')[0]);
        $('#startDate').val(threeMonthsAgo.toISOString().split('T')[0]);

        table.draw();
    });
});
</script>
@endsection
