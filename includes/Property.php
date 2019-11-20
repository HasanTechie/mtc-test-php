<?php

class Property extends DB
{
    public function select()
    {
        $query = "SELECT * FROM properties ORDER BY id DESC;";

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
        $fields['uuid'] = uniqid(mt_rand(), true);

        $fileName = $fields['uuid'] . '_' . $_FILES["image"]['name'];
        $this->uploadFile($fileName);

        $fields['image_full'] = $fileName;
        $fields['image_thumbnail'] = 'thumb_' . $fileName;
        $fields['created_at'] = $fields['updated_at'] = date('Y-m-d H:i:s');

        unset($fields['submit'], $fields['action'], $fields['id']);

        $implodeColumns = implode(', ', array_keys($fields));
        $implodeValues = implode(', :', array_keys($fields));


        $query = "INSERT INTO properties ($implodeColumns) VALUES (:" . $implodeValues . ")";

        $stmt = $this->connect()->prepare($query);

        foreach ($fields as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmtExec = $stmt->execute();

        if ($stmtExec) {
            $_SESSION['message'] = 'Property has been added';
            $_SESSION['message_type'] = 'success';
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
        unset($fields['submit'], $fields['action'], $fields['id']);


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
    }


    protected function uploadFile($fileName) //upload image and thumbnail
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
            $this->createThumb('uploads/' . $fileName, $fileExt, 'uploads/thumbs/thumb_' . $fileName, 200, 200);
        } else {
            echo 'Some Error Occured: <br>' . implode('<br>', $errors);
        }
    }

    protected function createThumb($target, $ext, $thumb_path, $w, $h) //create thumbnail
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
}