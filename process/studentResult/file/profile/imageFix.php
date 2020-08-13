<?php

define('db_host','localhost');
define('db_user','root');
define('db_pass','');
define('db_name','traco');
$conn = new mysqli(db_host, db_user, db_pass, db_name);

$result_student = $conn->query("SELECT * FROM student_data");
$result_employee = $conn->query("SELECT * FROM employee_data");

while ($row = $result_student->fetch_assoc()) {
    if ($row['photo'] != '') {
        $id = $row['id'];
        $photo = $row['photo'];

        $root_path = dirname(__FILE__).'/';
        $img_url = '../profile/'.$photo;
        $filename = basename($img_url);
        $filename = preg_replace('/\s+/', '', $filename);

        if($conn->query("UPDATE student_data SET photo = '$filename' WHERE id = '$id'"))
        {
            if (file_exists($img_url)) {
                $img_content = file_get_contents($img_url);
                file_put_contents($root_path.$filename, $img_content);
                unlink($img_url);
            }else{
                echo 'not found <br>';
            }
        }
    }
}

while ($row = $result_employee->fetch_assoc()) {
    if ($row['photo'] != '') {
        $id = $row['id'];
        $photo = $row['photo'];

        $root_path = dirname(__FILE__).'/';
        $img_url = '../profile/'.$photo;
        $filename = basename($img_url);
        $filename = preg_replace('/\s+/', '', $filename);

        if($conn->query("UPDATE employee_data SET photo = '$filename' WHERE id = '$id'"))
        {
            if (file_exists($img_url)) {
                $img_content = file_get_contents($img_url);
                file_put_contents($root_path.$filename, $img_content);
                unlink($img_url);
            }else{
                echo 'not found <br>';
            }
        }
    }
}


/* $root_path = dirname(__FILE__).'/';
$img_url = '../profile/2019-03-22-11-10-005c94fac88ecd4Lisa Anochiwa.jpg';

$filename = basename($img_url);
$filename = preg_replace('/\s+/', '', $filename);
$img_content = file_get_contents($img_url);
file_put_contents($root_path.$filename, $img_content);
unlink($img_url); */