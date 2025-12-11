<?php
require 'vendor/autoload.php';

$db = \Config\Database::connect();

$result = $db->query('DESCRIBE courses');

echo "Courses table structure:\n";
foreach ($result->getResult() as $row) {
    echo $row->Field . ' - ' . $row->Type . ' - ' . ($row->Null == 'YES' ? 'NULL' : 'NOT NULL') . ' - ' . ($row->Default ?? 'NULL') . "\n";
}
