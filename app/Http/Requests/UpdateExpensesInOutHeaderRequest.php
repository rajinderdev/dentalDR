<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpensesInOutHeaderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
			'ClinicID' => 'sometimes|string|max:255',
			'ExpenseCategory' => 'sometimes|string',
			'NoOfExpenseItems' => 'sometimes|string',
			'TotalAmount' => 'sometimes|numeric',
			'ExpenseDate' => 'sometimes|date',
			'Comments' => 'sometimes|string',
			'IsDeleted' => 'sometimes',
			'expense_items' => 'sometimes|array',
			'expense_items.*.ExpensesTypeID' => 'sometimes',
			'expense_items.*.OtherExpenses' => 'sometimes',
			'expense_items.*.Amount' => 'sometimes',
			'expense_items.*.PaidBy' => 'sometimes',
        ];
    }
}