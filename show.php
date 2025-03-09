<?php

// Ensure the 'file' parameter exists
if (isset($_GET['file'])) {
    $file = $_GET['file'];

    // Sanitize the file path to avoid directory traversal attacks
    $file = realpath($file);

    // Ensure the file is within project directory
    $projectRoot = realpath(__DIR__);
    if (strpos($file, $projectRoot) === 0 && is_file($file)) {
        // Display the source code of the specified file
        show_source($file);
    } else {
        echo "Invalid file path.";
    }
} else {
    show_source(__FILE__);
}