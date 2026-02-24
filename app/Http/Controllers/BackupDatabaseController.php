<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Process\Process;

class BackupDatabaseController extends Controller
{
    public function index()
    {
        return view('admin.backup-database.index');
    }

    public function createSqlBackup()
    {
        try {
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);

            $filename = 'backup_' . $database . '_' . date('Y-m-d_His') . '.sql';
            $tempPath = storage_path('app/' . $filename);

            // Build mysqldump command
            $command = [
                'mysqldump',
                '--host=' . $host,
                '--port=' . $port,
                '--user=' . $username,
            ];

            if (!empty($password)) {
                $command[] = '--password=' . $password;
            }

            $command[] = '--single-transaction';
            $command[] = '--routines';
            $command[] = '--triggers';
            $command[] = '--result-file=' . $tempPath;
            $command[] = $database;

            $process = new Process($command);
            $process->setTimeout(300); // 5 minutes
            $process->run();

            if (!$process->isSuccessful()) {
                // Fallback: generate SQL dump manually via PHP
                $this->generateSqlDumpManually($tempPath, $database);
            }

            if (!file_exists($tempPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create backup file.',
                ], 500);
            }

            Log::info('SQL backup created', ['file' => $filename, 'user' => Auth::user()->UserID ?? 'System']);

            return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('SQL backup failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function generateSqlDumpManually($filePath, $database)
    {
        $tables = DB::select('SHOW TABLES');
        $key = 'Tables_in_' . $database;

        $sql = "-- Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$database}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$key;

            // Get CREATE TABLE statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (!empty($createTable)) {
                $createSql = $createTable[0]->{'Create Table'} ?? '';
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createSql . ";\n\n";
            }

            // Get table data
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $columns = array_keys((array) $rows->first());
                $columnList = '`' . implode('`, `', $columns) . '`';

                foreach ($rows->chunk(100) as $chunk) {
                    $values = [];
                    foreach ($chunk as $row) {
                        $rowValues = [];
                        foreach ((array) $row as $value) {
                            if (is_null($value)) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = "'" . addslashes((string) $value) . "'";
                            }
                        }
                        $values[] = '(' . implode(', ', $rowValues) . ')';
                    }
                    $sql .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES\n" . implode(",\n", $values) . ";\n\n";
                }
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        file_put_contents($filePath, $sql);
    }

    public function exportToExcel()
    {
        try {
            $database = config('database.connections.mysql.database');
            $filename = 'backup_' . $database . '_' . date('Y-m-d_His') . '.xlsx';
            $tempPath = storage_path('app/' . $filename);

            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0);

            $tables = DB::select('SHOW TABLES');
            $key = 'Tables_in_' . $database;

            $sheetIndex = 0;
            foreach ($tables as $table) {
                $tableName = $table->$key;

                $rows = DB::table($tableName)->limit(50000)->get();
                if ($rows->isEmpty()) {
                    continue;
                }

                // Sheet name max 31 chars
                $sheetName = substr($tableName, 0, 31);

                $sheet = $spreadsheet->createSheet($sheetIndex);
                $sheet->setTitle($sheetName);

                // Write headers
                $columns = array_keys((array) $rows->first());
                $colIndex = 1;
                foreach ($columns as $col) {
                    $sheet->setCellValueByColumnAndRow($colIndex, 1, $col);
                    $sheet->getStyleByColumnAndRow($colIndex, 1)->getFont()->setBold(true);
                    $colIndex++;
                }

                // Write data rows
                $rowIndex = 2;
                foreach ($rows as $row) {
                    $colIndex = 1;
                    foreach ((array) $row as $value) {
                        $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $value);
                        $colIndex++;
                    }
                    $rowIndex++;
                }

                $sheetIndex++;
            }

            if ($sheetIndex === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found to export.',
                ], 422);
            }

            $spreadsheet->setActiveSheetIndex(0);

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);
            $spreadsheet->disconnectWorksheets();

            Log::info('Excel backup created', ['file' => $filename, 'user' => Auth::user()->UserID ?? 'System']);

            return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Excel backup failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Excel export failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
