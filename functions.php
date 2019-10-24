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

function truncate($string, $length, $dots = "...")
{
    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}

function addProperty()
{

    global $connection;

    $dA1 = [];
    foreach ($_POST as $key => $value) {
        $dA1[$key] = antiInjection($value);
    }

    $query = "INSERT INTO properties(uuid, county, country, town, description, address, image_full, image_thumbnail, num_bedrooms, num_bathrooms, price, property_type_id, type, created_at, updated_at)
                VALUES (";

    $query .= "'" . uniqid(mt_rand(), true) . "','" .
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

    if (mysqli_affected_rows($connection) > 0) {
        $_SESSION['message'] = 'Property has been added';
        $_SESSION['message_type'] = 'success';
    }
}

function displayEditProperty()
{
    global $connection;

    $query = "SELECT * FROM properties WHERE id=" . antiInjection($_GET['edit']) . ";";
    $result = mysqli_query($connection, $query)
    or die("Error at query " . $query . '-- ' . mysqli_errno($connection));

    if (count($result)) {
        $row = mysqli_fetch_assoc($result);
        return $row;
    }

//    if (mysqli_affected_rows($connection) > 0) {
//        $_SESSION['message']='Property record has been updated';
//        $_SESSION['message_type']='info';
//    }
}

function editProperty()
{
    global $connection;


    $dE1 = [];
    foreach ($_POST as $key => $value) {
        $dE1[$key] = antiInjection($value);
    }

    $query = "UPDATE properties SET 
                     county = '" . $dE1['county'] . "',
                     country = '" . $dE1['country'] . "',
                     town = '" . $dE1['town'] . "',
                     description = '" . $dE1['description'] . "',
                     address = '" . $dE1['address'] . "',
                     image_full = '" . $dE1['image_full'] . "',
                     county = '" . $dE1['county'] . "',
                     num_bedrooms = '" . $dE1['num_bedrooms'] . "',
                     num_bathrooms = '" . $dE1['num_bathrooms'] . "',
                     price = '" . $dE1['price'] . "',
                     property_type_id = '" . $dE1['property_type_id'] . "',
                     type = '" . $dE1['type'] . "',
                     updated_at = now() ";

    $query .= "WHERE id='" . antiInjection($_POST['id']) . "' ;";

    $result = mysqli_query($connection, $query) or die("Error at query " . $query . '-- ' . mysqli_errno($connection));
    if (mysqli_affected_rows($connection) > 0) {
        $_SESSION['message'] = 'Property record has been updated';
        $_SESSION['message_type'] = 'success';
    }
}

function deleteProperty()
{
    global $connection;

    $query = "DELETE FROM properties WHERE id=" . antiInjection($_GET['delete']) . ";";
    $result = mysqli_query($connection, $query)
    or die("Error at query " . $query . '-- ' . mysqli_errno($connection));

    if (mysqli_affected_rows($connection) > 0) {
        $_SESSION['message'] = 'Property record has been deleted';
        $_SESSION['message_type'] = 'danger';
    }
}
