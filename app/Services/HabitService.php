<?php

namespace App\Services;

use App\Models\Habit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use App\Helpers\EntityDataHelper;   
class HabitService
{
    /**
     * Get all habits with pagination
     */
    public function getAllHabits(int $perPage = 15): LengthAwarePaginator
    {
        return Habit::latest()->paginate($perPage);
    }

    /**
     * Get a specific habit by ID
     */
    public function getHabitById(int $id): ?Habit
    {
        return Habit::findOrFail($id);
    }

    /**
     * Create a new habit
     */
    public function createHabit(array $data): Habit
    {
        $dataToPersist = EntityDataHelper::prepareForCreation($data);
        $dataToPersist['HabitID'] = (string) Str::uuid();
        return Habit::create($dataToPersist);
    }

    /**
     * Update an existing habit
     */
    public function updateHabit(Habit $habit, array $data): bool
    {
        return $habit->update($data);
    }

    /**
     * Delete a habit
     */
    public function deleteHabit(Habit $habit): ?bool
    {
        return $habit->delete();
    }
}
