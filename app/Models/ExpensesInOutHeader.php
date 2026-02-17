<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\ExpensesInOutDetail;

/**
 * Class ExpensesInOutHeader
 * 
 * @property string $ExpensesHeaderID
 * @property string|null $ClinicID
 * @property string $ExpenseCategory
 * @property int $NoOfExpenseItems
 * @property float $TotalAmount
 * @property Carbon $ExpenseDate
 * @property string $CreatedBy
 * @property Carbon $CreatedOn
 * @property string $LastUpdatedBy
 * @property Carbon $LastUpdatedOn
 * @property string $Comments
 * @property bool $IsDeleted
 * @property string $rowguid
 * 
 * @property Collection|ExpensesInOutDetail[] $expenses_in_out_details
 *
 * @package App\Models
 */
class ExpensesInOutHeader extends Model
{
	protected $table = 'ExpensesInOutHeader';
	protected $primaryKey = 'ExpensesHeaderID';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'NoOfExpenseItems' => 'int',
		'TotalAmount' => 'float',
		'ExpenseDate' => 'datetime',
		'CreatedOn' => 'datetime',
		'LastUpdatedOn' => 'datetime',
		'IsDeleted' => 'bool'
	];

	protected $fillable = [
		'ClinicID',
		'ExpenseCategory',
		'NoOfExpenseItems',
		'TotalAmount',
		'ExpenseDate',
		'CreatedBy',
		'CreatedOn',
		'LastUpdatedBy',
		'LastUpdatedOn',
		'Comments',
		'IsDeleted',
		'rowguid'
	];

	protected static function boot()
	{
		parent::boot();
		static::creating(function ($model) {
			if (empty($model->ExpensesHeaderID)) {
				$model->ExpensesHeaderID = (string) Str::uuid(); // Auto-generate UUID
			}
		});
		
		static::created(function ($model) {
			// Auto-create ExpensesInOutDetail records after header is created
			if (isset($model->expense_items) && is_array($model->expense_items)) {
				foreach ($model->expense_items as $item) {
					// Skip items without required ExpensesTypeID
					if (empty($item['ExpensesTypeID'])) {
						continue;
					}
					
					ExpensesInOutDetail::create([
						'ExpensesHeaderID' => $model->ExpensesHeaderID,
						'ExpensesTypeID' => $item['ExpensesTypeID'],
						'OtherExpenses' => $item['OtherExpenses'] ?? null,
						'Amount' => $item['Amount'] ?? 0,
						'PaidBy' => $item['PaidBy'] ?? null,
					]);
				}
			} else {
				// Create a single detail record if no items array provided
				// Only create if we have a valid ExpensesTypeID
				if (!empty($model->ExpensesTypeID)) {
					ExpensesInOutDetail::create([
						'ExpensesHeaderID' => $model->ExpensesHeaderID,
						'ExpensesTypeID' => $model->ExpensesTypeID,
						'OtherExpenses' => $model->OtherExpenses ?? null,
						'Amount' => $model->TotalAmount ?? 0,
						'PaidBy' => $model->CreatedBy ?? null,
					]);
				}
			}
		});
	}

	protected function id(): Attribute
    {
		return Attribute::make(
        	get: fn (mixed $value, array $attributes) => $attributes['ExpensesHeaderID'],
		);
    }

	public function expenses_in_out_details()
	{
		return $this->hasMany(ExpensesInOutDetail::class, 'ExpensesHeaderID');
	}
}
