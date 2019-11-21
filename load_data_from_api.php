<?php
require __DIR__ . '/vendor/autoload.php';
require 'includes/basic.php';

spl_autoload_register(function ($className) {
    require_once 'includes/' . $className . '.php';
});

use GuzzleHttp\Client;

//mysqli_query($connection, 'TRUNCATE TABLE properties'); //Truncate table before loading new data

$connection = new DB();
$client = new Client();
$property = new Property();

$stmt = $connection->connect()->prepare("TRUNCATE TABLE properties; TRUNCATE TABLE property_type;"); //Truncate tables before loading new data
$stmt->execute();

$apiKey = getenv('API_KEY');

$res = $client->request('GET', "http://trialapi.craig.mtcdevserver.com/api/properties?page[number]=1&page[size]=100&api_key=$apiKey"); //first request to server API via Guzzle Client
$nextpage = json_decode($res->getBody())->first_page_url;

while (filter_var($nextpage, FILTER_VALIDATE_URL)) { //Looping through all pages of api;it will stop when next_page_url is null

    $res = $client->request('GET', $nextpage);

    $response = json_decode($res->getBody());
    $nextpage = $response->next_page_url;

    $dataArray = json_decode(json_encode($response->data), true);

    foreach ($dataArray as $value) {
        $property->store($value);
    }



//        $query = "INSERT INTO properties(uuid, county, country, town, description, address, image_full, image_thumbnail, latitude, longitude, num_bedrooms, num_bathrooms, price, property_type_id, type, created_at, updated_at)
//                VALUES (";
//
//        $query .= "'" . $dataArray['uuid'] . "','" .
//            $dataArray['county'] . "','" .
//            $dataArray['country'] . "','" .
//            $dataArray['town'] . "','" .
//            $dataArray['description'] . "',' " .
//            $dataArray['address'] . " ','" .
//            $dataArray['image_full'] . "','" .
//            $dataArray['image_thumbnail'] . "'," .
//            $dataArray['latitude'] . "," .
//            $dataArray['longitude'] . "," .
//            $dataArray['num_bedrooms'] . "," .
//            $dataArray['num_bathrooms'] . "," .
//            $dataArray['price'] . "," .
//            $dataArray['property_type_id'] . ",'" .
//            $dataArray['type'] . "','" .
//            $dataArray['created_at'] . "','" .
//            $dataArray['updated_at'] . "');";
//
//        $result = mysqli_query($connection, $query)
//        or die("Error at query " . $query . '-- ' . mysqli_errno($connection));

//    if (mysqli_affected_rows($connection) > 0) {
//        $_SESSION['message'] = '10000 property records has been loaded from API to database';
//        $_SESSION['message_type'] = 'success';
//    }
}
header('location: index.php');
