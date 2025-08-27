<?php
echo "Starting bootstrap test...\n";

// Test 1: PHP version
echo "PHP Version: " . PHP_VERSION . "\n";

// Test 2: Required extensions
$required_extensions = ['memcached', 'mysqli'];
foreach ($required_extensions as $ext) {
    echo "Extension $ext: " . (extension_loaded($ext) ? 'LOADED' : 'NOT LOADED') . "\n";
}

// Test 3: Include config
echo "Including config.php...\n";
try {
    include_once __DIR__ . '/../lib/config.php';
    echo "Config loaded successfully\n";
} catch (Exception $e) {
    echo "Config error: " . $e->getMessage() . "\n";
}

// Test 4: Include util
echo "Including util.php...\n";
try {
    include_once __DIR__ . '/../lib/util.php';
    echo "Util loaded successfully\n";
} catch (Exception $e) {
    echo "Util error: " . $e->getMessage() . "\n";
}

// Test 5: Include autoloader
echo "Including autoloader...\n";
try {
    include_once __DIR__ . '/../vendor/autoload.php';
    echo "Autoloader loaded successfully\n";
} catch (Exception $e) {
    echo "Autoloader error: " . $e->getMessage() . "\n";
}

// Test 6: Try to create Cache object
echo "Creating Cache object...\n";
try {
    $Cache = new Gazelle\Cache();
    echo "Cache created successfully\n";
} catch (Exception $e) {
    echo "Cache error: " . $e->getMessage() . "\n";
}

// Test 7: Try to create Debug object
echo "Creating Debug object...\n";
try {
    $Debug = new Gazelle\Debug($Cache, Gazelle\DB::DB());
    echo "Debug created successfully\n";
} catch (Exception $e) {
    echo "Debug error: " . $e->getMessage() . "\n";
}

echo "Bootstrap test complete\n";
?>