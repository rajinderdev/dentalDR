<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PatientWallet extends Model
{
    protected $primaryKey = 'WalletID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'WalletID',
        'PatientID',
        'FamilyID',
        'Balance',
        'Currency',
        'IsActive',
        'LastTransactionDate',
        'CreatedBy',
        'CreatedOn',
        'LastUpdatedBy',
        'LastUpdatedOn',
        'IsDeleted',
    ];

    protected $casts = [
        'Balance' => 'decimal:2',
        'IsActive' => 'boolean',
        'IsDeleted' => 'boolean',
        'LastTransactionDate' => 'datetime',
        'CreatedOn' => 'datetime',
        'LastUpdatedOn' => 'datetime',
    ];

    protected $dates = [
        'LastTransactionDate',
        'CreatedOn',
        'LastUpdatedOn'
    ];

    /**
     * Get the patient that owns the wallet.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'PatientID', 'PatientID');
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'FamilyID', 'FamilyID');
    }

    /**
     * Get all transactions for the wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'WalletID', 'WalletID')
            ->orderBy('CreatedOn', 'desc');
    }

    /**
     * Get the wallet's total credit amount.
     */
    public function getTotalCreditsAttribute(): float
    {
        return $this->transactions()
            ->where('TransactionType', 'CREDIT')
            ->where('Status', 'COMPLETED')
            ->sum('Amount');
    }

    /**
     * Get the wallet's total debit amount.
     */
    public function getTotalDebitsAttribute(): float
    {
        return $this->transactions()
            ->where('TransactionType', 'DEBIT')
            ->where('Status', 'COMPLETED')
            ->sum('Amount');
    }
}
