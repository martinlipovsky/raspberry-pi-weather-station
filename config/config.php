<?php
//DB Connection
$DB_HOST = "localhost"; //mysql host
$DB_USER = ""; //mysql user name
$DB_PASS = ""; //mysql password
$DB_NAME = ""; //mysql db name

//OPENWEATHERMAP.ORG config
$city = ""; //city name, check on OPENWEATHERMAP.ORG, If city exist
$unit = "metric"; //imperial or metric Units format
$days = "4"; //forecat days
$APPID = ""; //your APPID key from OPENWEATHERMAP.ORG

if ($unit == "metric") {
    $unit_val = "°C";    
} else {
    $unit_val = "F";    
}

//DB CONNECT
$dbhandle = mysql_connect($DB_HOST, $DB_USER, $DB_PASS)
or die("Unable to connect to MySQL");
$selected = mysql_select_db($DB_NAME,$dbhandle)
or die("Could not select database");
?>