<?php
require 'vendor/autoload.php';

$db = \Config\Database::connect();

try {
    // Try to update a course with course_code
    $db->query("UPDATE courses SET course_code = 'TEST123' WHERE id = 1");
    echo "Update successful - course_code column exists\n";
} catch (Exception $e) {
    echo "Update failed: " . $e->getMessage() . "\n";
}
