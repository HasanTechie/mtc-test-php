<?php
require __DIR__ . '/vendor/autoload.php';
require 'basic.php';
require 'functions.php';

use GuzzleHttp\Client;

mysqli_query($connection, 'TRUNCATE TABLE properties'); //Truncate table before loading new data

$client = new Client();

$apiKey = getenv('API_KEY');

for ($i = 1; $i <= 10; $i++) { //Looping through all pages of api
    $res = $client->request('GET', "http://trialapi.craig.mtcdevserver.com/api/properties?page[number]=$i&page[size]=100&api_key=$apiKey"); //doing request to server API via Guzzle Client
    $response = json_decode($res->getBody());

    $dataArray = [];
    foreach ($response->data as $responseInstance) {
        foreach ($responseInstance as $key => $value) {
            $dataArray[$key] = antiInjection($value); //Checking each field for SQL injections; One field contained Apostrophe(') which was breaking SQL code
        }

        $query = "INSERT INTO properties(uuid, county, country, town, description, address, image_full, image_thumbnail, latitude, longitude, num_bedrooms, num_bathrooms, price, property_type_id, type, created_at, updated_at)
                VALUES (";

        $query .= "'" . $dataArray['uuid'] . "','" .
            $dataArray['county'] . "','" .
            $dataArray['country'] . "','" .
            $dataArray['town'] . "','" .
            $dataArray['description'] . "',' " .
            $dataArray['address'] . " ','" .
            $dataArray['image_full'] . "','" .
            $dataArray['image_thumbnail'] . "'," .
            $dataArray['latitude'] . "," .
            $dataArray['longitude'] . "," .
            $dataArray['num_bedrooms'] . "," .
            $dataArray['num_bathrooms'] . "," .
            $dataArray['price'] . "," .
            $dataArray['property_type_id'] . ",'" .
            $dataArray['type'] . "','" .
            $dataArray['created_at'] . "','" .
            $dataArray['updated_at'] . "');";

        $result = mysqli_query($connection, $query)
        or die("Error at query " . $query . '-- ' . mysqli_errno($connection));
    }
    if (mysqli_affected_rows($connection) > 0) {
        $_SESSION['message'] = '10000 property records has been loaded from API to database';
        $_SESSION['message_type'] = 'success';
        header('location: index.php');
    }
}