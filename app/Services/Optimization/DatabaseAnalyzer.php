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

        // 3. Check for Table Fragmentation
        $recommendations = array_merge($recommendations, $this->checkFragmentation());

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

    private function checkFragmentation() {
        $issues = [];
        $tables = DB::select('SHOW TABLE STATUS');

        foreach ($tables as $table) {
            if ($table->Data_free > 0 && $table->Data_length > 0) {
                $fragmentation = ($table->Data_free / $table->Data_length) * 100;
                if ($fragmentation > 15) { // Threshold: 15% fragmentation
                    $issues[] = "Table '{$table->Name}' is " . round($fragmentation, 1) . "% fragmented. Run 'OPTIMIZE TABLE {$table->Name}' to reclaim space.";
                }
            }
        }

        return $issues;
    }
}
