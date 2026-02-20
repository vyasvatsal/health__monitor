<?php

namespace App\Services\Optimization;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseAnalyzer
{
    /**
     * Analyze critical tables for missing indexes.
     * @return array
     */
    public function analyze()
    {
        $recommendations = [];

        // 1. Check 'check_results' for indexes on created_at and status
        $recommendations = array_merge($recommendations, $this->checkTable('check_results', ['created_at', 'status', 'check_id']));

        // 2. Check 'incidents' for indexes on created_at and status
        $recommendations = array_merge($recommendations, $this->checkTable('incidents', ['created_at', 'status', 'store_id']));

        return $recommendations;
    }

    private function checkTable($tableName, $columns)
    {
        $issues = [];
        if (!Schema::hasTable($tableName)) {
            return [];
        }

        // Get existing indexes
        $indexes = DB::select("SHOW INDEXES FROM {$tableName}");
        $existingColumns = [];
        foreach ($indexes as $idx) {
            $existingColumns[] = $idx->Column_name;
        }

        foreach ($columns as $col) {
            if (!in_array($col, $existingColumns)) {
                $issues[] = "Missing index on table '{$tableName}', column '{$col}'. Adding this index will speed up queries.";
            }
        }

        return $issues;
    }
}
