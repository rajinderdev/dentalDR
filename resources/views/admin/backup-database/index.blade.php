@extends('layouts.admin')
@push('styles')
@endpush
@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Manage Backup</h1>

    <!-- Backup to SQL -->
    <div class="border border-gray-200 rounded-lg mb-6">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-700">Backup to SQL-Backup File</h2>
        </div>
        <div class="px-5 py-5 flex items-center gap-6">
            <div class="w-14 h-14 flex items-center justify-center bg-green-50 rounded-lg">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
            </div>
            <div class="flex gap-3">
                <button type="button" id="btnCreateBackup" onclick="createSqlBackup()"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    Create Backup
                </button>
                <button type="button" id="btnCancelBackup" onclick="cancelBackup()" disabled
                        class="px-5 py-2 bg-gray-400 text-white rounded-lg transition-colors text-sm font-medium cursor-not-allowed">
                    Cancel
                </button>
            </div>
            <div id="sqlBackupProgress" class="hidden flex items-center gap-2">
                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-gray-600">Creating backup...</span>
            </div>
        </div>
    </div>

    <!-- Backup to Excel -->
    <div class="border border-gray-200 rounded-lg mb-6">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-700">Backup to Excel</h2>
        </div>
        <div class="px-5 py-5 flex items-center gap-6">
            <div class="w-14 h-14 flex items-center justify-center bg-green-50 rounded-lg">
                <svg class="w-10 h-10 text-green-700" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zM8 17l2-3.5L8 10h1.5l1.25 2.5L12 10h1.5l-2 3.5 2 3.5H12l-1.25-2.5L9.5 17H8z"/>
                </svg>
            </div>
            <div class="flex gap-3">
                <button type="button" id="btnExportExcel" onclick="exportToExcel()"
                        class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                    Export to Excel
                </button>
            </div>
            <div id="excelProgress" class="hidden flex items-center gap-2">
                <svg class="animate-spin h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-gray-600">Exporting to Excel...</span>
            </div>
        </div>
    </div>

    <!-- Sync to Remote Database -->
    <div class="border border-gray-200 rounded-lg">
        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-700">Sync to Remote Database</h2>
        </div>
        <div class="px-5 py-5 flex items-center gap-6">
            <div class="w-14 h-14 flex items-center justify-center bg-blue-50 rounded-lg">
                <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <div class="flex gap-3">
                <button type="button" disabled
                        class="px-5 py-2 bg-gray-400 text-white rounded-lg text-sm font-medium cursor-not-allowed"
                        title="Remote database details will be configured later">
                    Sync
                </button>
            </div>
            <span class="text-sm text-gray-500 italic">Remote database details will be configured later.</span>
        </div>
    </div>
</div>
@endsection

@section('page_js')
<script>
var sqlBackupXhr = null;

function createSqlBackup() {
    $('#btnCreateBackup').prop('disabled', true).addClass('opacity-50');
    $('#btnCancelBackup').prop('disabled', false).removeClass('bg-gray-400 cursor-not-allowed').addClass('bg-red-600 hover:bg-red-700 cursor-pointer');
    $('#sqlBackupProgress').removeClass('hidden');

    // Use a hidden iframe approach for file download
    var url = "{{ route('admin.backup-database.sql') }}";

    sqlBackupXhr = $.ajax({
        url: url,
        type: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function(data, status, xhr) {
            var contentDisposition = xhr.getResponseHeader('Content-Disposition');
            var filename = 'backup.sql';
            if (contentDisposition) {
                var match = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (match && match[1]) {
                    filename = match[1].replace(/['"]/g, '');
                }
            }

            var blob = new Blob([data], { type: 'application/sql' });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);

            Swal.fire('Success!', 'SQL backup created and downloaded successfully.', 'success');
            resetSqlButtons();
        },
        error: function(xhr) {
            if (xhr.statusText === 'abort') return;
            var msg = 'Failed to create SQL backup.';
            try {
                var reader = new FileReader();
                reader.onload = function() {
                    try {
                        var json = JSON.parse(reader.result);
                        msg = json.message || msg;
                    } catch(e) {}
                    Swal.fire('Error!', msg, 'error');
                };
                reader.readAsText(xhr.response);
            } catch(e) {
                Swal.fire('Error!', msg, 'error');
            }
            resetSqlButtons();
        }
    });
}

function cancelBackup() {
    if (sqlBackupXhr) {
        sqlBackupXhr.abort();
        sqlBackupXhr = null;
    }
    Swal.fire('Cancelled', 'Backup process has been cancelled.', 'info');
    resetSqlButtons();
}

function resetSqlButtons() {
    $('#btnCreateBackup').prop('disabled', false).removeClass('opacity-50');
    $('#btnCancelBackup').prop('disabled', true).removeClass('bg-red-600 hover:bg-red-700 cursor-pointer').addClass('bg-gray-400 cursor-not-allowed');
    $('#sqlBackupProgress').addClass('hidden');
}

function exportToExcel() {
    $('#btnExportExcel').prop('disabled', true).addClass('opacity-50');
    $('#excelProgress').removeClass('hidden');

    var url = "{{ route('admin.backup-database.excel') }}";

    $.ajax({
        url: url,
        type: 'GET',
        xhrFields: {
            responseType: 'blob'
        },
        success: function(data, status, xhr) {
            var contentDisposition = xhr.getResponseHeader('Content-Disposition');
            var filename = 'backup.xlsx';
            if (contentDisposition) {
                var match = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (match && match[1]) {
                    filename = match[1].replace(/['"]/g, '');
                }
            }

            var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);

            Swal.fire('Success!', 'Excel backup created and downloaded successfully.', 'success');
            resetExcelButtons();
        },
        error: function(xhr) {
            var msg = 'Failed to export to Excel.';
            try {
                var reader = new FileReader();
                reader.onload = function() {
                    try {
                        var json = JSON.parse(reader.result);
                        msg = json.message || msg;
                    } catch(e) {}
                    Swal.fire('Error!', msg, 'error');
                };
                reader.readAsText(xhr.response);
            } catch(e) {
                Swal.fire('Error!', msg, 'error');
            }
            resetExcelButtons();
        }
    });
}

function resetExcelButtons() {
    $('#btnExportExcel').prop('disabled', false).removeClass('opacity-50');
    $('#excelProgress').addClass('hidden');
}
</script>
@endsection
