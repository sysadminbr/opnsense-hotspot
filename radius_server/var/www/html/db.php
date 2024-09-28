<?php
$db_user = "radius";
$db_pass = "radius";
$db_host = "localhost";
$db_port = 3306;
$db_name = "radius";


$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port) or die(mysqli_error());

