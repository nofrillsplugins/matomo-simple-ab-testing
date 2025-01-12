<?php

namespace Piwik\Plugins\SimpleABTesting\Dao;

use Piwik\Common;
use Piwik\Date;
use Piwik\Db;
use Piwik\DbHelper;

class LogExperiment
{
    public function createLog($parameters)
    {
    // Map array keys to column names and placeholders
        $columns = array_keys($parameters); // Get all keys for the column names
        $placeholders = array_map(fn($key) => ':' . $key, $columns); // Create named placeholders (:idsite, :experiment_name, etc.)

    // Construct the SQL query dynamically
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            Common::prefixTable('simple_ab_testing_log'),     // Table name
            implode(', ', $columns),                         // Columns (e.g., idsite, experiment_name, ...)
            implode(', ', $placeholders)                     // Corresponding placeholders (e.g., :idsite, :experiment_name, ...)
        );

    // Execute the database query with the array values
        Db::query($sql, $parameters); // Bind parameters using the $props array
    }
}
