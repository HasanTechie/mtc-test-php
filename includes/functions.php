<?php
function antiInjection($str) // for preventing SQL injections
{
    global $connection;
    return mysqli_real_escape_string($connection, $str);
}

function antiXSS($str) //to preventing Cross-site scripting (XSS)
{
    return htmlspecialchars($str);
}

function truncate($string, $length, $dots = "...")
{
    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}

/*function addProperty() //add property to database
{

    global $connection;

    $dA1 = [];
    foreach ($_POST as $key => $value) {
        $dA1[$key] = antiInjection($value);
    }

    $fileName = uniqid() . '_' . $_FILES["image"]['name'];
    uploadFile($fileName);

    $query = "INSERT INTO properties( county, country, town, description, address, num_bedrooms, num_bathrooms, price, property_type_id, type)
                VALUES (";

    $query .= "'" .
        $dA1['county'] . "','" .
        $dA1['country'] . "','" .
        $dA1['town'] . "','" .
        $dA1['description'] . "',' " .
        $dA1['address'] . " '," .
//        $fileName . "','thumb_" .
//        $fileName . "'," .
        $dA1['num_bedrooms'] . "," .
        $dA1['num_bathrooms'] . "," .
        $dA1['price'] . "," .
        $dA1['property_type_id'] . ",'" .
        $dA1['type'] . "');";

    $result = mysqli_query($connection, $query)
    or die("Error at query " . $query . '-- ' . mysqli_errno($connection));

    if (mysqli_affected_rows($connection) > 0) {
        $_SESSION['message'] = 'Property has been added';
        $_SESSION['message_type'] = 'success';
    }
}*/

function displayEditProperty() //display edit property form values
{
    global $connection;

    $query = "SELECT * FROM properties WHERE id=" . antiInjection($_GET['edit']) . ";";
    $result = mysqli_query($connection, $query)
    or die("Error at query " . $query . '-- ' . mysqli_errno($connection));

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $dE1 = [];
        foreach ($row as $key => $value) {
            $dE1[$key] = antiXSS($value);
        }
        return $dE1;
    }
}

function editProperty() //updated selected property
{
    global $connection;


    $dE1 = [];
    foreach ($_POST as $key => $value) {
        $dE1[$key] = antiInjection($value);
    }

    if (!empty($_FILES["image"]['name'])) {
        $fileName = uniqid() . '_' . $_FILES["image"]['name'];
        uploadFile($fileName);
    } else {
        $errors[] = 'No Image is provided.';
    }


    $query = "UPDATE properties SET 
                     county = '" . $dE1['county'] . "',
                     country = '" . $dE1['country'] . "',
                     town = '" . $dE1['town'] . "',
                     description = '" . $dE1['description'] . "',
                     address = '" . $dE1['address'] . "',";
    if (!empty($fileName)) {
        $query .= "  image_full = '" . $fileName . "',";
        $query .= "  image_thumbnail = 'thumb_" . $fileName . "',";
    }
    $query .= "      county = '" . $dE1['county'] . "',
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

function deleteProperty() //delete selected property
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

function generateSelect($name = '', $options = array(), $default = 1) //preselect the selected option from database
{
    $html = '<select class="form-control" name="' . $name . '">';
    foreach ($options as $option => $value) {
        if ($option == $default) {
            $html .= '<option value=' . $value . ' selected="selected">' . $option . '</option>';
        } else {
            $html .= '<option value=' . $value . '>' . $option . '</option>';
        }
    }
    $html .= '</select>';
    return $html;
}

function uploadFile($fileName) //upload image and thumbnail
{
    $fileExtArr = explode('.', $fileName);//make array of file.name.ext as    array(file,name,ext)
    $fileExt = strtolower(end($fileExtArr));//get last item of array of user file input
    $fileSize = $_FILES["image"]['size'];
    $fileTmp = $_FILES["image"]['tmp_name'];

    //which files we accept
    $allowed_files = ['jpg', 'png', 'gif'];

    //validate file size
    if ($fileSize > (1024 * 1024 * 2)) {
        $errors[] = 'Maximum 2MB files are allowed';
    }

    //validating file extension
    if (!in_array($fileExt, $allowed_files)) {
        $errors[] = 'only (' . implode(', ', $allowed_files) . ') files are allowed.';
    }

    //do other validations here if you need more

    //before uploading we will look at errors array if empty
    if (empty($errors)) {
        move_uploaded_file($fileTmp, 'uploads/' . $fileName);

        //here we can create thumbnails by createThumb() function
        //it takes 5 parametes
        //1- original image, 2- file extension, 3-thumb full path, 4- max width of thumb, 5-max height of thumb
        createThumb('uploads/' . $fileName, $fileExt, 'uploads/thumbs/thumb_' . $fileName, 200, 200);
    } else {
        echo 'Some Error Occured: <br>' . implode('<br>', $errors);
    }
}

function createThumb($target, $ext, $thumb_path, $w, $h) //create thumbnail
{
    list($w_orig, $h_orig) = getimagesize($target);
    $scale_ratio = $w_orig / $h_orig;
    if (($w / $h) > $scale_ratio)
        $w = $h * $scale_ratio;
    else
        $h = $w / $scale_ratio;

    if ($w_orig <= $w) {
        $w = $w_orig;
        $h = $h_orig;
    }
    $img = "";
    if ($ext == "gif")
        $img = imagecreatefromgif($target);
    else if ($ext == "png")
        $img = imagecreatefrompng($target);
    else if ($ext == "jpg")
        $img = imagecreatefromjpeg($target);

    $tci = imagecreatetruecolor($w, $h);
    imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
    imagejpeg($tci, $thumb_path, 80);
    imagedestroy($tci);
}