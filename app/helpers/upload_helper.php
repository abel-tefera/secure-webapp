<?php
function upload()
{
    // $target_dir = MAINROOT . "/public/img/";
    $target_dir = "/var/secure_files/";

    // $ext = explode(".", basename($_FILES["fileToUpload"]["name"]));

    $uploadedName = basename($_FILES["fileToUpload"]["name"]);
    $ext = strtolower(substr($uploadedName, strripos($uploadedName, '.') + 1));

    // $finfo = finfo_open(FILEINFO_MIME_TYPE);
    // if (finfo_file($finfo, $uploadedName) == false) {
    //     $uploadOk = 0;
    //     return array($uploadOk, "Sorry, only pdf files are allowed.");
    // }
    // finfo_close($finfo);

    $fileName = generateRandomString() . '.' . $ext;
    $target_file = $target_dir . $fileName;

    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $filecontent = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
    if (!preg_match("/^%PDF-/", $filecontent)) {
        $uploadOk = 0;
        return array($uploadOk, "Invalid PDF");
    }

    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $uploadOk = 0;
        return array($uploadOk, "Sorry, your file is too large.");
    }

    if (
        $imageFileType != "pdf"
    ) {
        $uploadOk = 0;
        return array($uploadOk, "Sorry, only PDF files are allowed.");
    }

    if ($uploadOk == 0) {
        return array($uploadOk, "Sorry, your file was not uploaded.");
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            return array($uploadOk, "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.", $fileName);
        } else {
            return array(0, "Sorry, there was an error uploading your file.");
        }
    }
}

function generateRandomString($length = 25)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
