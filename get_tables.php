<?php
// Simple database connection to show tables
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db = 'apotek_parahyangan_db';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
    $result = $conn->query('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME');
    
    echo "\n";
    echo "╔════════════════════════════════════════╗\n";
    echo "║       DATABASE TABLES                  ║\n";
    echo "║   Database: apotek_parahyangan_db      ║\n";
    echo "╚════════════════════════════════════════╝\n\n";
    
    $count = 0;
    $tables = [];
    while ($row = $result->fetch_assoc()) {
        $count++;
        $tables[] = $row['TABLE_NAME'];
        echo sprintf("%2d. %s\n", $count, $row['TABLE_NAME']);
    }
    
    echo "\n";
    echo "════════════════════════════════════════\n";
    echo "Total: $count tables\n";
    echo "════════════════════════════════════════\n\n";
    
    $conn->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
