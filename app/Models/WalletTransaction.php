<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{

    protected $primaryKey = 'TransactionID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'TransactionID',
        'WalletID',
        'PatientID',
        'FamilyID',
        'TreatmentDoneID',
        'ReceiptID',
        'Amount',
        'TransactionType',
        'ReferenceType',
        'ReferenceID',
        'Description',
        'BalanceBefore',
        'BalanceAfter',
        'Status',
        'CreatedBy',
        'CreatedOn',
        'IsDeleted',
    ];

    protected $casts = [
        'Amount' => 'decimal:2',
        'BalanceBefore' => 'decimal:2',
        'BalanceAfter' => 'decimal:2',
        'CreatedOn' => 'datetime',
        'IsDeleted' => 'boolean',
    ];

    protected $dates = [
        'CreatedOn',
    ];

    /**
     * Get the wallet that owns the transaction.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(PatientWallet::class, 'WalletID', 'WalletID');
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(PatientReceipt::class, 'ReceiptID', 'ReceiptID');
    }
}
