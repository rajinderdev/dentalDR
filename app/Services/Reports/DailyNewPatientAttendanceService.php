<?php

namespace App\Services\Reports;

use Illuminate\Support\Facades\DB;

class DailyNewPatientAttendanceService
{
    /**
     * Get daily new patient list report data based on filters.
     *
     * @param array $filters
     * @return array
     */
    public function getDailyNewPatientList(array $filters): array
        {
            $startDate = $filters['start_date'] ?? null;
            $endDate = $filters['end_date'] ?? null;

            if (!$startDate || !$endDate) {
                return [];
            }

            // First, get the base query with minimal data
            $baseQuery = \App\Models\Patient::query()
                ->whereBetween(DB::raw('DATE(Patient.RegistrationDate)'), [$startDate, $endDate])
                ->leftJoin('countries', 'countries.CountryID', '=', 'Patient.Country')
                ->select([
                    'Patient.PatientID',
                    'Patient.Gender',
                    'countries.CountryName',
                    'Patient.Age'
                ]);

            // Execute the query and process in chunks
            $result = [
                'Male Indian' => [],
                'Female Indian' => [],
                'Male NRI' => [],
                'Female NRI' => [],
            ];

            // Initialize all age groups with 0 counts
            $ageGroups = [
                '0-4', '5-9', '10-14', '15-19', '20-24', '25-29', '30-34', '35-39',
                '40-44', '45-49', '50-54', '55-59', '60-64', '65-69', '70-74',
                '75-79', '80-84', '>=85'
            ];

            foreach (['Male Indian', 'Female Indian', 'Male NRI', 'Female NRI'] as $category) {
                foreach ($ageGroups as $ageGroup) {
                    $result[$category][$ageGroup] = 0;
                }
            }

            // Process in chunks to reduce memory usage
            $baseQuery->chunk(1000, function ($patients) use (&$result) {
                foreach ($patients as $patient) {
                    $age = $patient->Age ?? 0;
                    $gender = strtolower($patient->Gender);
                    $isIndian = strtolower($patient->CountryName) === 'india';
                    
                    // Determine age group
                    $ageGroup = $this->getAgeGroup($age);
                    
                    // Determine category
                    if ($gender === 'm' && $isIndian) {
                        $category = 'Male Indian';
                    } elseif ($gender === 'f' && $isIndian) {
                        $category = 'Female Indian';
                    } elseif ($gender === 'm') {
                        $category = 'Male NRI';
                    } elseif ($gender === 'f') {
                        $category = 'Female NRI';
                    } else {
                        continue;
                    }
                    
                    // Increment the count
                    if (isset($result[$category][$ageGroup])) {
                        $result[$category][$ageGroup]++;
                    }
                }
            });

            return $result;
        }

        private function getAgeGroup(int $age): string
        {
            if ($age >= 85) return '>=85';
            $lower = floor($age / 5) * 5;
            $upper = $lower + 4;
            return "$lower-$upper";
        }
}
