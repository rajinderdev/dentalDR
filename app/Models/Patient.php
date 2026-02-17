<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class Patient
 * 
 * @property string $PatientID
 * @property string|null $ClinicID
 * @property string $ProviderID
 * @property string $Title
 * @property string $FirstName
 * @property string|null $LastName
 * @property string|null $Gender
 * @property Carbon|null $DOB
 * @property string $AddressLine1
 * @property string|null $AddressLine2
 * @property string|null $Street
 * @property string|null $Area
 * @property string $City
 * @property string $State
 * @property int $Country
 * @property string|null $ZipCode
 * @property string|null $PhoneNumber
 * @property string|null $MobileNumber
 * @property string|null $EmailAddress1
 * @property string|null $EmailAddress2
 * @property string|null $Occupation
 * @property string|null $ReferredBy
 * @property string|null $ReferralPatientID
 * @property string|null $ReferralProviderID
 * @property string|null $ReferralDescription
 * @property string|null $Imagethumbnail
 * @property string|null $ImagePath
 * @property Carbon|null $RegistrationDate
 * @property int|null $Status
 * @property string|null $WorkFlowInstanceID
 * @property Carbon $AddedOn
 * @property string|null $AddedBy
 * @property Carbon $LastUpdatedOn
 * @property string|null $LastUpdatedBy
 * @property int $CaseID
 * @property string $PatientCode
 * @property string|null $rowguid
 * @property string|null $PatientRefNo
 * @property string|null $PatientNotes
 * @property int|null $Age
 * @property string|null $FamilyID
 * @property bool|null $IsCreatedExport
 * @property Carbon|null $IsCreatedExportedOn
 * @property Carbon|null $IsLastUpdatedExportedOn
 * @property bool|null $IsupdatedExport
 * @property bool|null $IsActiveSMSSubscriber
 * @property bool|null $IsActiveEmailSubscriber
 * @property string|null $Nationality
 * @property string|null $PasswordHashSalt
 * @property string|null $MaritalStatus
 * @property string|null $Signatures
 * @property Carbon|null $SignatureDate
 * @property string|null $SignatureJson
 * @property int|null $ManualCaseId
 * @property string|null $CaseCodePrefix
 * @property string|null $SecondaryMobile
 * @property string|null $BloodGroup
 * @property bool|null $IsDead
 * 
 * @property Collection|ClinicLabWork[] $clinic_lab_works
 * @property Collection|PatientAddress[] $patient_addresses
 * @property Collection|PatientInsuranceDetail[] $patient_insurance_details
 * @property Collection|PatientAllergyAttribute[] $patient_allergy_attributes
 * @property Collection|PatientDentalHistoryAttribute[] $patient_dental_history_attributes
 * @property Collection|PatientDiagnosi[] $patient_diagnosis
 * @property Collection|PatientDocument[] $patient_documents
 * @property Collection|PatientInvestigation[] $patient_investigations
 * @property Collection|PatientMedicalAttribute[] $patient_medical_attributes
 * @property Collection|PatientReceipt[] $patient_receipts
 * @property Collection|PatientTreatment[] $patient_treatments
 * @property Collection|PatientTreatmentsDone[] $patient_treatments_dones
 * @property Collection|PatientTreatmentsPlanHeader[] $patient_treatments_plan_headers
 *
 * @package App\Models
 */
class Patient extends Model
{
	protected $table = 'Patient';
	protected $primaryKey = 'PatientID';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'DOB' => 'datetime',
		'Country' => 'int',
		'RegistrationDate' => 'datetime',
		'Status' => 'int',
		'AddedOn' => 'datetime',
		'LastUpdatedOn' => 'datetime',
		'CaseID' => 'int',
		'Age' => 'int',
		'IsCreatedExport' => 'bool',
		'IsCreatedExportedOn' => 'datetime',
		'IsLastUpdatedExportedOn' => 'datetime',
		'IsupdatedExport' => 'bool',
		'IsActiveSMSSubscriber' => 'bool',
		'IsActiveEmailSubscriber' => 'bool',
		'SignatureDate' => 'datetime',
		'ManualCaseId' => 'int',
		'IsDead' => 'bool',
		'MarriageAnniversary' => 'date',
		'VIP' => 'bool'
	];

	protected $fillable = [
		'PatientID',
		'ClinicID',
		'ProviderID',
		'Title',
		'FirstName',
		'LastName',
		'Gender',
		'DOB',
		'AddressLine1',
		'AddressLine2',
		'Street',
		'Area',
		'City',
		'State',
		'Country',
		'ZipCode',
		'PhoneNumber',
		'MobileNumber',
		'EmailAddress1',
		'EmailAddress2',
		'Occupation',
		'ReferredBy',
		'ReferralPatientID',
		'ReferralProviderID',
		'ReferralDescription',
		'Imagethumbnail',
		'ImagePath',
		'RegistrationDate',
		'Status',
		'WorkFlowInstanceID',
		'AddedOn',
		'AddedBy',
		'LastUpdatedOn',
		'LastUpdatedBy',
		'CaseID',
		'PatientCode',
		'rowguid',
		'PatientRefNo',
		'PatientNotes',
		'Age',
		'FamilyID',
		'IsCreatedExport',
		'IsCreatedExportedOn',
		'IsLastUpdatedExportedOn',
		'IsupdatedExport',
		'IsActiveSMSSubscriber',
		'IsActiveEmailSubscriber',
		'Nationality',
		'PasswordHashSalt',
		'MaritalStatus',
		'Signatures',
		'SignatureDate',
		'SignatureJson',
		'ManualCaseId',
		'CaseCodePrefix',
		'SecondaryMobile',
		'BloodGroup',
		'IsDead',
		'VIP',
		'AbhaID',
		'Designation',
		'Building',
		'Building_id',
		'Notify',
		'MarriageAnniversary',
	];

	protected static $elasticsearch;

	public static function getElasticsearchClient()
{
    if (!self::$elasticsearch) {
        try {
            self::$elasticsearch = ClientBuilder::create()
                ->setHosts([env('ELASTICSEARCH_HOSTS', 'localhost:9200')])
                ->setConnectionPool('\Elasticsearch\ConnectionPool\StaticNoPingConnectionPool')
                ->setRetries(2)
                ->build();
        } catch (\Exception $e) {
            Log::warning('Failed to create Elasticsearch client: ' . $e->getMessage());
            return null;
        }
    }
    return self::$elasticsearch;
}

	/**
	 * Check if Elasticsearch is available and healthy
	 */
	public static function isElasticsearchAvailable(): bool
	{
		$client = self::getElasticsearchClient();
		if (!$client) {
			return false;
		}

		try {
			$response = $client->ping();
			return $response;
		} catch (\Exception $e) {
			Log::debug('Elasticsearch ping failed: ' . $e->getMessage());
			return false;
		}
	}

	public function toSearchArray()
	{
		// Eager load appointments if not loaded
		if (!$this->relationLoaded('appointments')) {
			$this->load('appointments');
		}
		return [
			'id' => $this->PatientID,
			'title' => $this->Title,
			'first_name' => $this->FirstName,
			'last_name' => $this->LastName,
			'phone' => $this->PhoneNumber,
			'mobile' => $this->MobileNumber,
			'email' => $this->EmailAddress1,
			'email2' => $this->EmailAddress2,
			// Add appointment dates for search/filtering
			'appointments' => $this->appointments->map(function ($a) {
				return [
					'appointment_id' => $a->AppointmentID,
					'start_date' => optional($a->StartDateTime)->format('Y-m-d'),
					'end_date' => optional($a->EndDateTime)->format('Y-m-d'),
					'status' => $a->Status,
				];
			})->toArray(),
		];
	}

	public static function search($query)
	{
		$client = self::getElasticsearchClient();
		
		// Fallback to database search if Elasticsearch is not available
		if (!$client) {
			return self::where(function ($q) use ($query) {
				$q->where('FirstName', 'like', '%' . $query . '%')
				  ->orWhere('LastName', 'like', '%' . $query . '%')
				  ->orWhere('EmailAddress1', 'like', '%' . $query . '%')
				  ->orWhere('EmailAddress2', 'like', '%' . $query . '%')
				  ->orWhere('PhoneNumber', 'like', '%' . $query . '%')
				  ->orWhere('MobileNumber', 'like', '%' . $query . '%');
			})->get();
		}
		
		try {
			$params = [
				'index' => 'patients',
				'body' => [
					'query' => [
						'multi_match' => [
							'query' => $query,
							'fields' => ['first_name', 'last_name', 'email', 'email2', 'phone', 'mobile'],
							'type' => 'phrase_prefix',
						]
					]
				]
			];
			
			$results = $client->search($params);
			$ids = collect($results['hits']['hits'])->pluck('_source.id')->all();
			return self::whereIn('PatientID', $ids)->get();
		} catch (\Exception $e) {
			Log::warning('Elasticsearch search failed, falling back to database: ' . $e->getMessage());
			
			// Fallback to database search
			return self::where(function ($q) use ($query) {
				$q->where('FirstName', 'like', '%' . $query . '%')
				  ->orWhere('LastName', 'like', '%' . $query . '%')
				  ->orWhere('EmailAddress1', 'like', '%' . $query . '%')
				  ->orWhere('EmailAddress2', 'like', '%' . $query . '%')
				  ->orWhere('PhoneNumber', 'like', '%' . $query . '%')
				  ->orWhere('MobileNumber', 'like', '%' . $query . '%');
			})->get();
		}
	}

	public function indexToElasticsearch()
	{
		// Always eager load appointments for indexing
		$this->loadMissing('appointments');
		$client = self::getElasticsearchClient();
		
		// Check if Elasticsearch client is available
		if (!$client) {
			Log::debug('Elasticsearch client not available, skipping indexing for patient: ' . $this->PatientID);
			return;
		}
		
		try {
			$client->index([
				'index' => 'patients',
				'id' => $this->PatientID,
				'body' => $this->toSearchArray(),
			]);
		} catch (\Exception $e) {
			Log::warning('Failed to index patient to Elasticsearch: ' . $e->getMessage());
		}
	}

protected static function boot()
{
	parent::boot();
	static::creating(function ($model) {
		if (empty($model->PatientID)) {
			$model->PatientID = (string) Str::uuid(); // Auto-generate UUID
		}
	});
}

	protected static function booted()
	{
		static::saved(function ($patient) {
			$patient->indexToElasticsearch();
		});
		static::deleted(function ($patient) {
			$client = self::getElasticsearchClient();
			
			// Check if Elasticsearch client is available
			if (!$client) {
				Log::debug('Elasticsearch client not available, skipping deletion for patient: ' . $patient->PatientID);
				return;
			}
			
			try {
				$client->delete([
					'index' => 'patients',
					'id' => $patient->PatientID,
				]);
			} catch (\Exception $e) {
				Log::warning('Failed to delete patient from Elasticsearch: ' . $e->getMessage());
			}
		});
	}

	protected function id(): Attribute
	{
		return Attribute::make(
			get: fn (mixed $value, array $attributes) => $attributes['PatientID'],
		);
	}
	protected function fullName(): Attribute
	{
		return Attribute::make(
			get: fn (mixed $value, array $attributes) => trim(
				($attributes['Title'] ?? '') . ' ' .
				(trim($attributes['FirstName']) ?? '') . ' ' .
				($attributes['LastName'] ?? '')
			),
		);
	}

	public function clinic_lab_works()
	{
		return $this->hasMany(ClinicLabWork::class, 'PatientID');
	}

	public function consents()
	{
		return $this->hasOne(PatientConsentDetail::class, 'PatientID');
	}

	public function patient_addresses()
	{
		return $this->hasMany(PatientAddress::class, 'PatientID');
	}

	public function patient_insurance_details()
	{
		return $this->hasMany(PatientInsuranceDetail::class, 'PatientID');
	}

	public function patient_allergy_attributes()
	{
		return $this->hasMany(PatientAllergyAttribute::class, 'PatientID');
	}

	public function patient_dental_history_attributes()
	{
		return $this->hasMany(PatientDentalHistoryAttribute::class, 'PatientID');
	}

	public function patient_diagnosis()
	{
		return $this->hasMany(PatientDiagnosis::class, 'PatientID');
	}

	public function patient_documents()
	{
		return $this->hasMany(PatientDocument::class, 'PatientID');
	}

	public function patient_investigations()
	{
		return $this->hasMany(PatientInvestigation::class, 'PatientID');
	}

	public function patient_medical_attributes()
	{
		return $this->hasMany(PatientMedicalHistoryAttribute::class, 'PatientID');
	}

	public function patient_receipts()
	{
		return $this->hasMany(PatientReceipt::class, 'PatientID');
	}

	public function patient_treatments()
	{
		return $this->hasMany(PatientTreatment::class, 'PatientID');
	}

	public function patient_treatments_done()
	{
		return $this->hasMany(PatientTreatmentsDone::class, 'PatientID');
	}


    public function patient_communication_group()
	{
		return $this->hasOne(PatientCommunicationGroup::class, 'PatientID');
	}


	public function patient_treatments_plan_headers()
	{
		return $this->hasMany(PatientTreatmentsPlanHeader::class, 'PatientID');
	}

	public function store(array $data)
	{
		// Ensure CaseID is an integer
		if (!isset($data['CaseID'])) {
			$data['CaseID'] = $this->generateCaseID();
		}

		return Patient::create($data);
	}

	// Function to generate a CaseID integer
	private function generateCaseID()
	{
		return rand(1000, 9999); // Generates a random integer (adjust as needed)
	}

	public function appointments()
	{
		return $this->hasMany(Appointment::class, 'PatientID', 'PatientID');
	}
	public function treatments()
	{
		return $this->hasMany(PatientTreatmentsDone::class, 'PatientID', 'PatientID');
	}

	public function diagnoses()
	{
		return $this->hasMany(PatientDiagnosis::class, 'PatientID', 'PatientID');
	}

	public function patient_notes()
	{
		return $this->hasMany(PatientNote::class, 'PatientID', 'PatientID');
	}
	public function patient_habits()
	{
		return $this->hasMany(PatientHabit::class, 'PatientID', 'PatientID');
	}
	public function patient_prescriptions()
	{
		return $this->hasMany(PatientPrescription::class, 'PatientID', 'PatientID');
	}
	

	public function personal_reminders()
	{
		return $this->hasMany(PersonalReminder::class, 'PatientID', 'PatientID');
	}
	public function family()
    {
        return $this->hasMany(Family::class, 'FamilyID', 'FamilyID');
    }
    
    /**
     * Get the building associated with the patient.
     */
    public function building()
    {
        return $this->belongsTo(Building::class, 'Building_id', 'id');
    }
    
    /**
     * Get the medical insurances associated with the patient.
     */
    public function patient_medical_insurances()
    {
        return $this->hasMany(PatientMedicalInsurance::class, 'PatientID', 'PatientID')
            ->where('IsDeleted', false);
    }

    /**
     * Get the package usages associated with the patient.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patient_package_usages()
    {
        return $this->hasMany(PatientPackageUsage::class, 'PatientID', 'PatientID')
            ->where('IsDeleted', false);
    }
}
