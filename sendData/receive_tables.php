<?php
// Destination database connection details
$destinationHost = 'localhost'; // Destination database host
$destinationUsername = 'destination_user'; // Destination database username
$destinationPassword = 'destination_password'; // Destination database password
$destinationDatabase = 'destination_db'; // Destination database name

// Check if the request contains a file
if (isset($_FILES['table_file'])) {
    $file = $_FILES['table_file'];

    // Check for any upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "Error uploading file: " . $file['error'];
        exit;
    }

    // Move the uploaded file to a temporary location
    $tmpFilePath = '/tmp/' . basename($file['name']);
    move_uploaded_file($file['tmp_name'], $tmpFilePath);

    // Connect to the destination database
    $destinationConnection = mysqli_connect($destinationHost, $destinationUsername, $destinationPassword, $destinationDatabase);

    // Check for database connection errors
    if (!$destinationConnection) {
        echo 'Destination database connection failed: ' . mysqli_connect_error();
        exit;
    }

    // Import the SQL file into the destination database
    $query = "LOAD DATA INFILE '{$tmpFilePath}' INTO TABLE " . basename($file['name'], '.sql');
    $result = mysqli_query($destinationConnection, $query);
    if (!$result) {
        echo "Error importing table " . basename($file['name'], '.sql') . ": " . mysqli_error($destinationConnection);
    } else {
        echo "Table " . basename($file['name'], '.sql') . " imported successfully.";
    }

    // Close the destination database connection
    mysqli_close($destinationConnection);

    // Clean up the temporary file
    unlink($tmpFilePath);
} else {
    echo "No file uploaded.";
}
?>
