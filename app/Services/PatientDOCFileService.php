<?php

namespace App\Services;

use App\Models\PatientDOCFile;
use App\Http\Resources\PatientDOCFileResource;
use App\Models\Patient;
use App\Models\PatientDOCFolder;
use App\Models\PatientDOCServerDocumentDetail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;

class PatientDOCFileService
{
    /**
     * Get a paginated list of Patient Document Files.
     *
     * @param int $perPage
     * @return array
     */
    public function getDOCFiles(Patient $patient, int $perPage): array
    {
        $data = PatientDOCFile::where('PatientID', $patient->id)->paginate($perPage);

        return [
            'doc_files' => $data,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }
    /**
     * Get a paginated list of  Document Files.
     *
     * @param int $perPage
     * @return array
     */
    public function getAllDocument(int $perPage): array
    {
        $data = PatientDOCFile::orderBy('CreatedOn','desc')->paginate($perPage);

        return [
            'doc_files' => $data,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }

    /**
     * Create a new Patient Document File with file upload handling.
     *
     * @param array $data
     * @return PatientDOCFile
     */
    public function createDocFile(array $data): PatientDOCFile
    {
        // Debug: Log the data array to see if ID is being set
        Log::info('PatientDOCFileService createDocFile data:', $data);
        
        // Handle file upload if present
        if (isset($data['file']) && $data['file'] instanceof UploadedFile) {
            $file = $data['file'];
            $fileName = $data['PatientID'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store file in public disk to ensure it's accessible
            // Format DocumentType by replacing spaces with underscores for folder names
            $documentTypeFolder = str_replace(' ', '_', strtoupper(trim($data['DocumentType'])));
            
            if(array_key_exists('ClinicID',$data) && $data['ClinicID']){
                $storagePath = 'eclinicalguidancedocuments/ECG' . $data['ClinicID'] . '/ECGPLUS/' . $documentTypeFolder;
                $filePath = $file->storeAs($storagePath, $fileName, 'public');
            }
            elseif(array_key_exists('PatientID',$data) && $data['PatientID']){
                $patient = Patient::where('PatientID',$data['PatientID'])->first();
                $storagePath = 'eclinicalguidancedocuments/ECG' . $patient->ClinicID. '/ECGPLUS/' . $documentTypeFolder;
                $data['ClinicID'] = $patient->ClinicID;
                $filePath = $file->storeAs($storagePath, $fileName, 'public');
            }
            else{
                $filePath = $file->storeAs($documentTypeFolder, $fileName, 'public');
            }
            
            $folder = PatientDOCFolder::where('Title',$data['DocumentType'])->first();
            // Add file information to data array
            $data['FileName'] = $fileName;
            $data['VirtualFilePath'] = 'storage/' . $filePath;
            $data['PhysicalFilePath'] = env('APP_URL', 'http://localhost') . '/storage/' . $filePath;
            $data['FolderId'] = $folder?$folder->FolderId:1;
            $data['FileSize'] = $file->getSize();
            $data['FileType'] = $file->getMimeType();
            $data['UploadedFileName'] = $file->getClientOriginalName();
        }

        // Remove the file from data array as it's not a model field
        unset($data['file']);

        return PatientDOCFile::create($data);
    }

    public function updateDocFile(PatientDOCFile $pdocfile, array $data): PatientDOCFile
    {
        $pdocfile->update($data);
        $pdocfile->fresh();

        return $pdocfile;
    }
    
    /**
     * Get a single Patient Document File by ID.
     *
     * @param int $id
     * @return PatientDOCFile|null
     */
    public function getDocFileById(int $id): ?PatientDOCFile
    {
        return PatientDOCFile::find($id);
    }
    
    /**
     * Delete a Patient Document File by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteDocFile(int $id): bool
    {
        $docFile = PatientDOCFile::find($id);
        if ($docFile) {
            // Delete the file from storage
            if ($docFile->PhysicalFilePath) {
                // Extract the relative path from the URL
                $relativePath = str_replace(env('APP_URL', 'http://localhost') . '/storage/', '', $docFile->PhysicalFilePath);
                Storage::disk('public')->delete($relativePath);
            }
            
            // Delete the record from database
            return $docFile->delete();
        }
        
        return false;
    }
    public function updatePhysicalFilePath(Request $request): bool
    {
        try {
            $processedCount = 0;
            $updatedCount = 0;

            // Process records in chunk{s to handle large datasets efficiently
            if($request->has('type') && $request->type == 'patientdoc_serverdocumentdetails'){
                PatientDOCServerDocumentDetail::chunk(1000, function ($docFiles) use (&$processedCount, &$updatedCount) {
                    foreach ($docFiles as $docFile) {
                        $processedCount++;

                        if ($docFile->AbsolutePath) {
                            // Replace everything before eclinicalguidancedocuments with the API URL
                            $newPath = $docFile->AbsolutePath;
                            $newPath = preg_replace('/^(?:[A-Z]:[\/\\\\]Applications[\/\\\\])?/', 'https://dental.stgserver.co.in/api/storage/', $newPath);

                            // Convert backslashes to forward slashes for proper URL format
                            $newPath = str_replace('\\', '/', $newPath);

                            // Replace spaces with underscores in the path
                            $newPath = str_replace(' ', '_', $newPath);

                            // Update the record if the path has changed
                            if ($newPath !== $docFile->AbsolutePath) {
                                $docFile->update(['AbsolutePath' => $newPath]);
                                $updatedCount++;
                            }
                        }
                    }
                });

            }
            else{
                 PatientDOCFile::chunk(1000, function ($docFiles) use (&$processedCount, &$updatedCount) {
                    foreach ($docFiles as $docFile) {
                        $processedCount++;

                        if ($docFile->PhysicalFilePath) {
                            // Replace everything before eclinicalguidancedocuments with the API URL
                            $newPath = $docFile->PhysicalFilePath;
                            $newPath = preg_replace('/^(?:[A-Z]:[\/\\\\]Applications[\/\\\\])?/', 'https://dental.stgserver.co.in/api/storage/', $newPath);

                            // Convert backslashes to forward slashes for proper URL format
                            $newPath = str_replace('\\', '/', $newPath);

                            // Replace spaces with underscores in the path
                            $newPath = str_replace(' ', '_', $newPath);

                            // Update the record if the path has changed
                            if ($newPath !== $docFile->PhysicalFilePath) {
                                $docFile->update(['PhysicalFilePath' => $newPath]);
                                $updatedCount++;
                            }
                        }
                    }
                });
            }
           

            Log::info("Physical file path update completed. Processed: {$processedCount}, Updated: {$updatedCount}");

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating physical file paths: ' . $e->getMessage());
            return false;
        }
    }
}