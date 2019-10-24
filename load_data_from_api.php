<?php
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client();

$res = $client->request('GET', "http://trialapi.craig.mtcdevserver.com/api/properties?page[number]=$i&page[size]=100&api_key=3NLTTNlXsi6rBWl7nYGluOdkl2htFHug"); //doing request to server API via Guzzle Client

$response = json_decode($res->getBody());

var_dump($response);