<?php

$sqlitePath = 'C:\Users\IT LUNARAY\Herd\apotek-parahyangan-web\database\database.sqlite';

if (!file_exists($sqlitePath)) {
    die("SQLite file not found.\n");
}

try {
    $db = new PDO("sqlite:$sqlitePath");
    
    echo "Tables in SQLite database:\n";
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        $count = $db->query("SELECT count(*) FROM `$table`")->fetchColumn();
        echo "- $table ($count rows)\n";
    }
    
    echo "\nSample articles:\n";
    if (in_array('articles', $tables)) {
        $articles = $db->query("SELECT title FROM articles LIMIT 5")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($articles as $title) {
            echo "  - $title\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
