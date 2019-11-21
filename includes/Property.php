<?php

class Property extends DB
{

    protected $recordsPerPage = 100;

    public function index()
    {
        $query = "SELECT * FROM properties ORDER BY id DESC ";

        $query = $this->paging($query, $this->recordsPerPage);

        $result = $this->connect()->query($query);

        if ($result->rowCount()) {

            while ($row = $result->fetch()) {

                $data[] = $row;

            }

            return $data;
        }
    }

    public function store($fields)
    {

        if (isset($fields['action'])) { //If data request is from form

            $fields = $this->validate($fields);

            if ($fields) {

                $fields['uuid'] = uniqid(mt_rand(), true);

                $fileName = $fields['uuid'] . '_' . $_FILES["image"]['name'];
                $this->uploadFile($fileName);

                $fields['image_full'] = $fileName;
                $fields['image_thumbnail'] = 'thumb_' . $fileName;
                $fields['created_at'] = $fields['updated_at'] = date('Y-m-d H:i:s');

                $stmtExec = $this->insertStatement($fields, 'properties');

                if ($stmtExec) {
                    $_SESSION['message'] = 'Property has been added';
                    $_SESSION['message_type'] = 'success';
                }
            } else {
                $_SESSION['message'] = 'Following fields cannot be empty: ' . $_SESSION['message'];
                $_SESSION['message_type'] = 'danger';
            }

        } else { //If data request is from API

            $propertyTypeArray = json_decode(json_encode($fields['property_type']), true);
            $this->insertStatement($propertyTypeArray, 'property_type');

            unset($fields['property_type']);
            $stmtExec = $this->insertStatement($fields, 'properties');

            if ($stmtExec) {
                $_SESSION['message'] = 'Properties has been added';
                $_SESSION['message_type'] = 'success';
            }

        }
    }

    public function edit($id)
    {
        $query = "SELECT * FROM properties WHERE id = :id";

        $stmt = $this->connect()->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $dataArrayE1 = [];
            foreach ($result as $key => $value) {
                $dataArrayE1[$key] = antiXSS($value);  //to preventing Cross-site scripting (XSS)
            }
            return $dataArrayE1;
        }
    }

    public function update($fields)
    {

        $query = "";
        $i = 1;
        $id = $fields['id'];

        $fields = $this->validate($fields);

        if ($fields) {
            if (!empty($_FILES["image"]['name'])) {
                $fileName = uniqid() . '_' . $_FILES["image"]['name'];
                $this->uploadFile($fileName);
            }

            if (!empty($fileName)) {
                $fields['image_full'] = $fileName;
                $fields['image_thumbnail'] = 'thumb_' . $fileName;
            }
            $fields['created_at'] = $fields['updated_at'] = date('Y-m-d H:i:s');

            $totalFields = count($fields);

            foreach ($fields as $key => $value) {
                if ($i == $totalFields) {
                    $setQuery = "$key = :" . $key;
                    $query = $query . $setQuery;
                } else {
                    $setQuery = "$key = :" . $key . ", ";
                    $query = $query . $setQuery;
                    $i++;
                }
            }

            $query = "UPDATE properties SET " . $query;
            $query .= " WHERE id = " . $id;

            $stmt = $this->connect()->prepare($query);

            foreach ($fields as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmtExec = $stmt->execute();

            if ($stmtExec) {
                $_SESSION['message'] = 'Property record has been updated';
                $_SESSION['message_type'] = 'success';
            }
        } else {
            $_SESSION['message'] = 'Following fields cannot be empty: ' . $_SESSION['message'];
            $_SESSION['message_type'] = 'danger';
        }
    }

    public function destroy($id)
    {
        $query = "DELETE FROM properties WHERE id = :id";
        $stmt = $this->connect()->prepare($query);
        $stmt->bindValue(":id", $id);
        $stmtExec = $stmt->execute();
        if ($stmtExec) {
            $_SESSION['message'] = 'Property record has been deleted';
            $_SESSION['message_type'] = 'danger';
        }

    }

    protected function insertStatement($fields, $table)
    {
        $implodeColumns = implode(', ', array_keys($fields));
        $implodeValues = implode(', :', array_keys($fields));

        $query = "INSERT IGNORE INTO $table ($implodeColumns) VALUES (:" . $implodeValues . ")";

        $stmt = $this->connect()->prepare($query);

        foreach ($fields as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmtExec = $stmt->execute();

        return $stmtExec;
    }


    protected function uploadFile($fileName)
    {//upload image and thumbnail
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
            $this->createThumb('uploads/' . $fileName, $fileExt, 'uploads/thumbs/thumb_' . $fileName, 200, 200);
        } else {
            echo 'Some Error Occured: <br>' . implode('<br>', $errors);
        }
    }

    protected function createThumb($target, $ext, $thumb_path, $w, $h)
    { //create thumbnail
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

    protected function validate($fields)
    {
        $errors = 0;


        $_SESSION['message'] = "<ul>";
        if (empty($_FILES["image"]['name']) && $fields['action'] != 'edit') {
            $_SESSION['message'] .= "<li>Image Field</li>";
            $errors = 1;
        }
        unset($fields['submit'], $fields['action'], $fields['id']);

        foreach ($fields as $key => $field) {
            if (empty($field)) {
                $key = str_replace('_', ' ', $key);
                $_SESSION['message'] .= "<li>" . ucfirst($key) . "</li>";
                $errors = 1;
            }
        }
        $_SESSION['message'] .= "</ul>";

        if ($errors == 0) {
            return $fields;
        }
    }

    public function paging($query, $recordPerPage)
    {
        $startingPosition = 0;
        if (isset($_GET['page_no'])) {
            $startingPosition = ($_GET['page_no'] - 1) * $recordPerPage;
        }
        $query = $query . " limit $startingPosition,$recordPerPage ; ";
        return $query;
    }

    public function pageLink($query = "SELECT * FROM properties ORDER BY id DESC ")
    {
        $self = $_SERVER['PHP_SELF'];

        $stmt = $this->connect()->prepare($query);
        $stmt->execute();

        $totalRecords = $stmt->rowCount();

        $output = "";

        if ($totalRecords > 0) {

            $output .= '<ul class="pagination">';

            $totalPages = ceil($totalRecords / $this->recordsPerPage);
            $currentPage = 1;
            if (isset($_GET["page_no"])) {
                $currentPage = $_GET["page_no"];
            }
            if ($currentPage != 1) {
                $previous = $currentPage - 1;
                $output .= "<li class=\"page-item\"><a class=\"page-link\" href='" . $self . "?page_no=1'>First</a></li>";
                $output .= "<li class=\"page-item\"><a class=\"page-link\" href='" . $self . "?page_no=" . $previous . "'>Previous</a></li>";
            }

            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i == $currentPage) {
                    $output .= "<li  class=\"page-item\"><a class=\"page-link\" href='" . $self . "?page_no=" . $i . "' style='color:red;'>" . $i . "</a></li>";
                } else {
                    $output .= "<li  class=\"page-item\"><a class=\"page-link\" href='" . $self . "?page_no=" . $i . "'>" . $i . "</a></li>";
                }
            }

            if ($currentPage != $totalPages) {
                $next = $currentPage + 1;
                $output .= "<li  class=\"page-item\"><a class=\"page-link\" href='" . $self . "?page_no=" . $next . "'>Next</a></li>";
                $output .= "<li  class=\"page-item\"><a class=\"page-link\" href='" . $self . "?page_no=" . $totalPages . "'>last</a></li>";
            }
            $output .= '</ul>';

            return $output;
        }
    }
}