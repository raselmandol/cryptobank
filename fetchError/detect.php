<?php
//database details
$destinationHost = 'localhost';
$destinationUsername = 'destination_user';
$destinationPassword = 'destination_password';
$destinationDatabase = 'destination_db';

// Fetch/get all anchors
$webServerURL = 'http://example.com/';
$response = file_get_contents($webServerURL);

//checking the request
if ($response === false) {
    echo "Failed to fetch URLs from the web server.";
    exit;
}

//response_gathering
$urls = array();
$dom = new DOMDocument();
@$dom->loadHTML($response); 
$links = $dom->getElementsByTagName('a');
foreach ($links as $link) {
    $url = $link->getAttribute('href');
    //data_filter
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $urls[] = $url;
    }
}

//error pages
$errorPages = array();

foreach ($urls as $url) {
    //sending http request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    //checking the request
    if ($response === false) {
        echo "Failed to fetch page: {$url}.";
        continue;
    }

    //http response
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

   //storing the data(http errors)
    if ($httpCode >= 400) {
        //connecting database
        $destinationConnection = mysqli_connect($destinationHost, $destinationUsername, $destinationPassword, $destinationDatabase);

       
        if (!$destinationConnection) {
            echo 'Destination database connection failed: ' . mysqli_connect_error();
            exit;
        }

       //inserting data
        $query = "INSERT INTO error_pages (url, http_code, response) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($destinationConnection, $query);
        mysqli_stmt_bind_param($stmt, 'sis', $url, $httpCode, $response);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            echo "Error page for URL {$url} with HTTP code {$httpCode} inserted into the database successfully.";
        } else {
            echo "Failed to insert error page for URL {$url} with HTTP code {$httpCode} into the database: " . mysqli_error($destinationConnection);
        }

        
        mysqli_close($destinationConnection);
    }

    curl_close($ch);
}
?>
