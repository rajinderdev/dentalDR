<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpensesInOutHeaderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
			'ClinicID' => 'required|string|max:255',
			'ExpenseCategory' => 'required|string',
			'NoOfExpenseItems' => 'required|string',
			'TotalAmount' => 'required',
			'ExpenseDate' => 'required|date',
			'Comments' => 'required|string',
			'IsDeleted' => 'required',
            'expense_items' => 'required|array',
            'expense_items.*.ExpensesTypeID' => 'required',
            'expense_items.*.OtherExpenses' => 'required',
            'expense_items.*.Amount' => 'required',
            'expense_items.*.PaidBy' => 'nullable',

        ];
    }
}