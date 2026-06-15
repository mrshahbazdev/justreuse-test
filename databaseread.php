<?php

// --- 1. CONFIGURATION: Apne database ki details yahan likhein ---
$dbHost = '127.0.0.1';      // Aksar 'localhost' hi hota hai
$dbUser = 'justreuseddbusr';           // Aapka database username
$dbPass = 'HJyHuJtdyfxK5ZsPkgDb';               // Aapka database password
$dbName = 'sstechjustreused'; // Jis database ko analyze karna hai, uska naam yahan likhein

// Database connection
$host = 'localhost';
$dbname = 'sstechjustreused';
$username = 'justreuseddbusr';
$password = 'HJyHuJtdyfxK5ZsPkgDb';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // 1. Sabhi tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables) . "<br><br>";
    
    // 2. Har table ka details
    foreach ($tables as $table) {
        echo "Table: $table<br>";
        
        // Columns
        $columns = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo " - {$col['Field']} ({$col['Type']}) - Key: {$col['Key']}<br>";
        }
        
        // Relationships
        $relations = $pdo->query("
            SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = '$table' AND REFERENCED_TABLE_NAME IS NOT NULL
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        if ($relations) {
            echo "Relationships:<br>";
            foreach ($relations as $rel) {
                echo " - {$rel['COLUMN_NAME']} → {$rel['REFERENCED_TABLE_NAME']}.{$rel['REFERENCED_COLUMN_NAME']}<br>";
            }
        }
        echo "<br>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>