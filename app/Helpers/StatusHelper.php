<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * Map status string values to their corresponding status IDs
     * 
     * @param string|null $status
     * @return int
     */
    public static function getStatusId(?string $status): int
    {
        if (empty($status)) {
            return 1; // Default to assigned status if no status provided
        }

        $statusMap = [
            'Assigned' => 1,
            'Completed' => 2,
            'Cancelled' => 3,
        ];

        $normalizedStatus = strtolower(trim($status));

        return $statusMap[$normalizedStatus] ?? 1; // Default to assigned if invalid status
    }

    /**
     * Get all available status mappings
     * 
     * @return array
     */
    public static function getStatusMappings(): array
    {
        return [
            'Assigned' => 1,
            'Completed' => 2,
            'Cancelled' => 3,
        ];
    }

    /**
     * Check if a status string is valid
     * 
     * @param string|null $status
     * @return bool
     */
    public static function isValidStatus(?string $status): bool
    {
        if (empty($status)) {
            return true; // Allow null/empty as it defaults to assigned
        }

        $validStatuses = array_keys(self::getStatusMappings());
        $normalizedStatus = strtolower(trim($status));

        return in_array($normalizedStatus, $validStatuses);
    }
}