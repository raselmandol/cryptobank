<?php
// Source database connection details
$sourceHost = 'localhost'; // Source database host
$sourceUsername = 'source_user'; // Source database username
$sourcePassword = 'source_password'; // Source database password
$sourceDatabase = 'source_db'; // Source database name

// Destination website URL to send the database tables
$destinationUrl = 'https://www.example.com/receive_tables.php';

// Connect to the source database
$sourceConnection = mysqli_connect($sourceHost, $sourceUsername, $sourcePassword, $sourceDatabase);

// Check for database connection errors
if (!$sourceConnection) {
    die('Source database connection failed: ' . mysqli_connect_error());
}

// Get a list of all tables in the source database
$tables = [];
$query = "SHOW TABLES";
$result = mysqli_query($sourceConnection, $query);
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

// Loop through each table and export as SQL files
foreach ($tables as $table) {
    // Export the table as SQL file
    $query = "SELECT * INTO OUTFILE '/tmp/{$table}.sql' FROM {$table}";
    $result = mysqli_query($sourceConnection, $query);
    if (!$result) {
        echo "Error exporting table {$table}: " . mysqli_error($sourceConnection) . "\n";
    } else {
        echo "Table {$table} exported successfully.\n";
    }

    // Send the SQL file to the destination website using cURL
    $file = '/tmp/' . $table . '.sql';
    $curl = curl_init($destinationUrl);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, [
        'table_file' => new CURLFile($file)
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        echo "Error sending table {$table}: " . curl_error($curl) . "\n";
    } else {
        echo "Table {$table} sent successfully.\n";
    }
    curl_close($curl);
}

// Close the source database connection
mysqli_close($sourceConnection);
?>
