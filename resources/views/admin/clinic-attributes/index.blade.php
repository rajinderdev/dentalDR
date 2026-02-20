@extends('layouts.admin')
@push('styles')
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Clinic Attributes</h1>
    </div>

    <!-- Search -->
    <div class="mb-6 flex flex-wrap gap-4 items-center">
        <label for="searchInput" class="text-sm font-medium text-gray-700">Search in Item</label>
        <input type="text" id="searchInput" placeholder="Search..."
               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
        <a href="{{ route('admin.clinic-attributes.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Clear Filters
        </a>
    </div>

    <!-- Attributes Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" id="clinic-attributes-table">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Attribute Name</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Attribute Description</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Attribute Value</th>
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
    var table = $('#clinic-attributes-table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('admin.clinic-attributes.index') }}",
            data: function(d) {
                d.search = $('#searchInput').val();
            }
        },
        columns: [
            {
                data: 'AttributeName',
                name: 'ClinicAttributesMaster.AttributeName',
                orderable: true
            },
            {
                data: 'AttributeDescription',
                name: 'ClinicAttributesMaster.AttributeDescription',
                orderable: true,
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: 'AttributeValue',
                name: 'AttributeValue',
                orderable: false,
                width: '250px'
            },
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            processing: '<div class="flex items-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Processing...</span></div>',
            zeroRecords: 'No attributes found',
            info: 'Showing _START_ to _END_ of _TOTAL_ attributes',
            infoEmpty: 'No attributes available',
            infoFiltered: '(filtered from _MAX_ total attributes)',
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

    // Custom search handler
    $('#searchInput').on('keyup', function() {
        table.draw();
    });

    // Save on blur (when user clicks away from input)
    $(document).on('blur', '.attr-value-input', function() {
        var $input = $(this);
        var newValue = $input.val();
        var originalValue = $input.data('original');
        var attrName = $input.data('name');

        if (newValue === originalValue) return;

        $.ajax({
            url: "{{ route('admin.clinic-attributes.update-value') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                AttributeName: attrName,
                AttributeValue: newValue
            },
            success: function(res) {
                if (res.success) {
                    $input.data('original', newValue);
                    $input.addClass('border-green-500');
                    setTimeout(function() {
                        $input.removeClass('border-green-500');
                    }, 1500);
                } else {
                    Swal.fire('Error!', res.message || 'Failed to update.', 'error');
                    $input.val(originalValue);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to update attribute value.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMessage, 'error');
                $input.val(originalValue);
            }
        });
    });

    // Save on Enter key
    $(document).on('keyup', '.attr-value-input', function(e) {
        if (e.key === 'Enter') {
            $(this).blur();
        }
    });
});
</script>
@endsection
