<?php
/**
 * Database Import Script
 * This script imports data from an SQL export file
 */

// Path to your SQL export file
$sql_file = 'database_export.sql'; // CHANGE THIS if needed

if (!file_exists($sql_file)) {
    die("Error: SQL file not found at: $sql_file\n");
}

try {
    $pdo = new PDO("sqlite:schoolproject.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read SQL file
    $sql = file_get_contents($sql_file);
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $