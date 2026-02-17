<?php

namespace App\Services;

use App\Models\ExpensesInOutHeader;
use App\Models\ExpensesInOutDetail;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Resources\ExpensesInOutHeaderResource;
use App\Helpers\EntityDataHelper;
use Illuminate\Support\Facades\Auth;
class ExpensesInOutHeaderService
{
    /**
     * Get a paginated list of Expenses In Out Headers.
     *
     * @param int $perPage
     * @param array $filters
     * @return array
     */
    public function getExpensesInOutHeaders(int $perPage, array $filters = []): array
    {
        $query = ExpensesInOutHeader::where('IsDeleted', false);
        
        // Apply filters
        if (!empty($filters['start_date'])) {
            $query->whereDate('ExpenseDate', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('ExpenseDate', '<=', $filters['end_date']);
        }
       
        if (!empty($filters['expense_category'])) {
            $query->where('ExpenseCategory', $filters['expense_category']);
        }
        
        $data = $query->orderBy('CreatedOn', 'desc')->paginate($perPage);

        return [
            'headers' => $data,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ];
    }

    /**
     * Create a new Expenses In Out Header.
     *
     * @param array $data The validated data for creating expenses header
     * @return ExpensesInOutHeader The newly created expenses header model
     */
    public function createExpense(array $data): ExpensesInOutHeader
    {
        $validatedData = EntityDataHelper::prepareForCreation($data);
        $expensesHeader = ExpensesInOutHeader::create($validatedData);
        
        // Auto-create ExpensesInOutDetail records
        $this->createExpenseDetails($expensesHeader, $data);
        
        return $expensesHeader;
    }

    /**
     * Create expense detail records for the given header.
     *
     * @param ExpensesInOutHeader $expensesHeader
     * @param array $data
     * @return void
     */
    private function createExpenseDetails(ExpensesInOutHeader $expensesHeader, array $data): void
    {
        // If expense items data is provided, create detail records
        if (isset($data['expense_items']) && is_array($data['expense_items'])) {
            foreach ($data['expense_items'] as $item) {
                ExpensesInOutDetail::create([
                    'ExpensesHeaderID' => $expensesHeader->ExpensesHeaderID,
                    'ExpensesTypeID' => $item['ExpensesTypeID'] ?? null,
                    'OtherExpenses' => $item['OtherExpenses'] ?? null,
                    'Amount' => $item['Amount'] ?? 0,
                    'PaidBy' => $item['PaidBy'] ?? Auth::user()->UserID ?? null,
                ]);
            }
        } else {
            // Create a single detail record if no items array provided
            ExpensesInOutDetail::create([
                'ExpensesHeaderID' => $expensesHeader->ExpensesHeaderID,
                'ExpensesTypeID' => $data['ExpensesTypeID'] ?? null,
                'OtherExpenses' => $data['OtherExpenses'] ?? null,
                'Amount' => $data['Amount'] ?? 0,
                'PaidBy' => Auth::user()->UserID ?? null,
            ]);
        }
    }

    /**
     * Update an existing expense record.
     *
     * @param ExpensesInOutHeader $expensesInOutHeader The expense model to update
     * @param array $data The validated data for updating expense
     * @return ExpensesInOutHeader The updated expense model
     */
    public function updateExpense(ExpensesInOutHeader $expensesInOutHeader, array $data): ExpensesInOutHeader
    {
        $validatedData = EntityDataHelper::prepareForUpdate($data);
        $expensesInOutHeader->update($validatedData);
        
        // Update expense details if provided
        if (isset($data['expense_items']) && is_array($data['expense_items'])) {
            $this->updateExpenseDetails($expensesInOutHeader, $data['expense_items']);
        }
        
        return $expensesInOutHeader;
    }

    /**
     * Update expense detail records for given header.
     *
     * @param ExpensesInOutHeader $expensesHeader
     * @param array $expenseItems
     * @return void
     */
    private function updateExpenseDetails(ExpensesInOutHeader $expensesHeader, array $expenseItems): void
    {
        // Get existing detail records for this header
        $existingDetails = ExpensesInOutDetail::where('ExpensesHeaderID', $expensesHeader->ExpensesHeaderID)->get();
        
        // Delete existing details that are not in the update
        $itemIds = array_filter(array_column($expenseItems, 'ExpensesDetailID'));
        if (!empty($itemIds)) {
            ExpensesInOutDetail::where('ExpensesHeaderID', $expensesHeader->ExpensesHeaderID)
                ->whereNotIn('ExpensesDetailID', $itemIds)
                ->delete();
        }
        
        // Update or create detail records
        foreach ($expenseItems as $item) {
            if (isset($item['ExpensesDetailID'])) {
                // Update existing detail
                ExpensesInOutDetail::where('ExpensesDetailID', $item['ExpensesDetailID'])
                    ->update([
                        'ExpensesTypeID' => $item['ExpensesTypeID'] ?? null,
                        'OtherExpenses' => $item['OtherExpenses'] ?? null,
                        'Amount' => $item['Amount'] ?? 0,
                        'PaidBy' => $item['PaidBy'] ?? null,
                    ]);
            } else {
                // Create new detail
                ExpensesInOutDetail::create([
                    'ExpensesHeaderID' => $expensesHeader->ExpensesHeaderID,
                    'ExpensesTypeID' => $item['ExpensesTypeID'] ?? null,
                    'OtherExpenses' => $item['OtherExpenses'] ?? null,
                    'Amount' => $item['Amount'] ?? 0,
                    'PaidBy' => $item['PaidBy'] ?? null,
                ]);
            }
        }
    }

    public function deleteExpense(ExpensesInOutHeader $expensesInOutHeader): ExpensesInOutHeader
    {
        $expensesInOutHeader->update([
            'IsDeleted' => true,
        ]);
        return $expensesInOutHeader;
    }
}