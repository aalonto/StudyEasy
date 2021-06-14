<?php
use Aws\S3\Exception\S3Exception;

$tmpFile = $_FILES['img']['tmp_name'];

if (isset($_POST['upload'])) {
    try {
       $s3->putObject([
            'Bucket' => 'studyeasy',
            'Key' =>  $_SESSION['username'],
            'Body' => fopen($tmpFile, 'r'),
            'ACL'    => 'public-read'
        ]);
        header("Location: editProfile.php");
        exit();
    } catch (S3Exception $e) {
        echo "There was an error uploading the file.\n";
    }
}
