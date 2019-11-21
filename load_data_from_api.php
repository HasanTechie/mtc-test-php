<?php
require __DIR__ . '/vendor/autoload.php';
require 'includes/basic.php';

use GuzzleHttp\Client;

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
}
header('location: index.php');
