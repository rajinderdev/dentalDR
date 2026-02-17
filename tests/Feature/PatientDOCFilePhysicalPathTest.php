<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Patient;
use App\Models\PatientDOCFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PatientDOCFilePhysicalPathTest extends TestCase
{
    use RefreshDatabase;

    protected $patient;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create a patient for testing
        $this->patient = Patient::factory()->create();
    }

    /** @test */
    public function it_stores_full_physical_file_path()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('document.jpg');

        $data = [
            'PatientID' => $this->patient->id,
            'ClinicID' => '1',
            'DocumentID' => 1,
            'StatusID' => 1,
            'Description' => 'Test document with full path',
            'file' => $file,
            'PublishOn' => '2023-01-01',
            'ExpirationOn' => '2024-01-01',
            'ReferenceNo' => 'REF-001'
        ];

        $response = $this->postJson("/api/patients/{$this->patient->id}/doc/file", $data);

        $response->assertStatus(201);

        // Get the created document
        $docFile = PatientDOCFile::find($response->json('data.docFile.id'));

        // Check that PhysicalFilePath contains the full path
        $this->assertNotNull($docFile->PhysicalFilePath);
        $this->assertStringContainsString('document.jpg', $docFile->PhysicalFilePath);
        $this->assertStringContainsString('patient_documents', $docFile->PhysicalFilePath);
        
        // On Windows, the path will contain backslashes, on Unix systems forward slashes
        // So we'll check that it's an absolute path by ensuring it contains the storage directory
        $this->assertTrue(
            str_contains($docFile->PhysicalFilePath, 'storage') || 
            str_contains($docFile->PhysicalFilePath, ':\\') ||  // Windows drive letter
            str_starts_with($docFile->PhysicalFilePath, '/')    // Unix absolute path
        );
    }
}