<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DoctorScope implements Scope
{
    /**
     * Cache the scope column per table to avoid repeated Schema lookups.
     *
     * @var array<string, string|null>
     */
    private static array $scopeColumnCache = [];

    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();
        if (!$user || !$user->role || strtolower($user->role->RoleName) !== 'doctor') {
            return;
        }

        $scopeColumn = $this->resolveScopeColumn($model);
        if (!$scopeColumn) {
            // Model/table does not have ProviderID/DoctorID; don't apply any restriction.
            return;
        }

        // Find the provider record for this user
        $provider = \App\Models\Provider::where('UserID', $user->UserID)->first();
        if (!$provider) {
            // If no provider found, return no results
            $builder->whereRaw('1 = 0');
            return;
        }

        // If the base table is aliased (rare), use the alias in the where.
        $tableForWhere = $this->resolveFromAliasOrTable($builder, $model);
        $builder->where($tableForWhere . '.' . $scopeColumn, $provider->ProviderID);
    }

    private function resolveScopeColumn(Model $model): ?string
    {
        $table = $model->getTable();
        if (array_key_exists($table, self::$scopeColumnCache)) {
            return self::$scopeColumnCache[$table];
        }

        $column = null;
        try {
            if (Schema::hasColumn($table, 'ProviderID')) {
                $column = 'ProviderID';
            } elseif (Schema::hasColumn($table, 'DoctorID')) {
                $column = 'DoctorID';
            }
        } catch (\Throwable $e) {
            // If schema inspection fails (eg during early bootstrap), avoid breaking requests.
            $column = null;
        }

        self::$scopeColumnCache[$table] = $column;
        return $column;
    }

    private function resolveFromAliasOrTable(Builder $builder, Model $model): string
    {
        $from = $builder->getQuery()->from;
        if (!is_string($from) || trim($from) === '') {
            return $model->getTable();
        }

        $fromTrimmed = trim($from);

        // Handle: "table as alias"
        $parts = preg_split('/\s+as\s+/i', $fromTrimmed);
        if (is_array($parts) && count($parts) === 2) {
            return trim($parts[1]);
        }

        // Handle: "table alias"
        $chunks = preg_split('/\s+/', $fromTrimmed);
        if (is_array($chunks) && count($chunks) === 2) {
            return trim($chunks[1]);
        }

        return $model->getTable();
    }
}
