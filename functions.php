<?php
function antiInjection($str)
{
    global $connection;
    return mysqli_real_escape_string($connection, $str);
}

function antiXSS($str)
{
    return htmlspecialchars($str);
}

function addData()
{

    global $connection;

    $dA1 = [];
    foreach ($_POST as $key => $value) {
        $dA1[$key] = antiInjection($value);
    }

    $query = "INSERT INTO properties(uuid, county, country, town, description, address, image_full, image_thumbnail, num_bedrooms, num_bathrooms, price, property_type_id, type, created_at, updated_at)
                VALUES (";

    $query .= "'" . uniqid(mt_rand(),true) . "','" .
        $dA1['county'] . "','" .
        $dA1['country'] . "','" .
        $dA1['town'] . "','" .
        $dA1['description'] . "',' " .
        $dA1['address'] . " ','" .
        $dA1['image_full'] . "','" .
        $dA1['image_full'] . "'," .
        $dA1['num_bedrooms'] . "," .
        $dA1['num_bathrooms'] . "," .
        $dA1['price'] . "," .
        $dA1['property_type_id'] . ",'" .
        $dA1['type'] . "',now(),now());";

    $result = mysqli_query($connection, $query)
    or die("Error at query " . $query . '-- ' . mysqli_errno($connection));

}