<?php
// Simple test script to verify the system is working
require_once 'vendor/autoload.php';

use CodeIgniter\Config\Services;

// Initialize CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

try {
    // Test database connection
    $db = \Config\Database::connect();
    echo "✅ Database connection successful\n";
    
    // Test announcements table
    $announcements = $db->table('announcements')->get()->getResultArray();
    echo "✅ Announcements table accessible\n";
    echo "📊 Found " . count($announcements) . " announcements\n";
    
    foreach ($announcements as $announcement) {
        echo "   - " . $announcement['title'] . " (Created: " . $announcement['created_at'] . ")\n";
    }
    
    // Test users table
    $users = $db->table('users')->get()->getResultArray();
    echo "✅ Users table accessible\n";
    echo "👥 Found " . count($users) . " users\n";
    
    foreach ($users as $user) {
        echo "   - " . $user['name'] . " (" . $user['role'] . ")\n";
    }
    
    echo "\n🎉 System test completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
