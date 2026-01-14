<?php

$path = 'C:\Users\IT LUNARAY\Downloads\u237530081_apotek_db (1).sql';
$content = file_get_contents($path);

$keywords = ['articles', 'article_categories', 'tags', 'article_tag', 'products', 'sliders', 'site_settings'];

foreach ($keywords as $kw) {
    echo "$kw: " . (stripos($content, $kw) !== false ? "FOUND" : "NOT FOUND") . "\n";
}

// Also check for CREATE TABLE and INSERT INTO
if (stripos($content, 'INSERT INTO `articles`') !== false) {
    echo "Articles data: FOUND\n";
} else {
    echo "Articles data: NOT FOUND\n";
}
