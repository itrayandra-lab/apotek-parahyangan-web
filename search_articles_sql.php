<?php

$path = 'C:\Users\IT LUNARAY\Herd\apotek-parahyangan-web\temp\u237530081_apotek_db.sql';
$content = file_get_contents($path);

$keywords = ['articles', 'article_categories', 'tags', 'article_tag', 'products', 'sliders'];

foreach ($keywords as $kw) {
    echo "$kw: " . (stripos($content, $kw) !== false ? "FOUND" : "NOT FOUND") . "\n";
}
