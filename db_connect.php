<?php
$host = 'ssql205.infinityfree.com';
$user = 'if0_43393963';
$pass = 'JCdGJ1hIwA';
$dbname = 'if0_43393963_scholarship_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
