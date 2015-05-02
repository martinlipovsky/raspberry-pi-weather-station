<?php
include("./config/config.php"); 
  
$current = simplexml_load_file("http://api.openweathermap.org/data/2.5/weather?q=" . $city . "&mode=xml&units=" . $unit . "&cnt=" . $days . "&APPID=" . $APPID . "")or die("Error: Cannot create object Feed");

$wind_val = number_format((float)$current->wind->speed->attributes()->value, 1, '.', '');
$wind_dir = number_format((float)$current->wind->direction->attributes()->value, 1, '.', '');
$wind_name = $current->wind->direction->attributes()->name;
$weather_icon_value = $current->weather->attributes()->icon;
$weather_icon_name = $current->weather->attributes()->value;
$precipitation = number_format((float)$current->precipitation->attributes()->value, 1, '.', '');
if (($current->clouds->attributes()->value) <= "0") {
    $cloud = $current->clouds->attributes()->name; 
} else {
    $cloud = $current->clouds->attributes()->value;
}
$sunrise = date("H:i:s", strtotime($current->city->sun->attributes()->rise));
$sunset = date("H:i:s", strtotime($current->city->sun->attributes()->set));

//curent weather
mysql_query('UPDATE openweather SET wind_val="' . mysql_real_escape_string($wind_val) . '",
wind_dir="' . mysql_real_escape_string($wind_dir) . '",
wind_name="' . mysql_real_escape_string($wind_name) . '",
weather_icon_value="' . mysql_real_escape_string($weather_icon_value) . '",
weather_icon_name="' . mysql_real_escape_string($weather_icon_name) . '",
precipitation="' . mysql_real_escape_string($precipitation) . '",
cloud="' . mysql_real_escape_string($cloud) . '",
sunrise="' . mysql_real_escape_string($sunrise) . '",
sunset="' . mysql_real_escape_string($sunset) . '"
WHERE id=1') or die(mysql_error());

//X days forecast
$daily = simplexml_load_file("http://api.openweathermap.org/data/2.5/forecast/daily?q=" . $city . "&mode=xml&units=" . $unit . "&cnt=" . $days . "&APPID=7da70f9161655d45eabc4946cbc7f157")or die("Error: Cannot create object Feed");

$result = $daily->xpath('//time');
$i = 0;
foreach($result as $later =>$weather)
  {
    $i++;
    $wind_val_forecast = number_format((float)$weather->windSpeed->attributes()->mps, 1, '.', '');    
    $wind_name_forecast = $weather->windDirection->attributes()->name;
    $weather_icon_value_forecast = $weather->symbol->attributes()->var;
    $weather_icon_name_forecast = $weather->symbol->attributes()->name;
    $precipitation_forecast = number_format((float)$weather->precipitation->attributes()->value, 1, '.', '');
    if (($current->clouds->attributes()->value) <= "0") {
        $cloud_forecast = $weather->clouds->attributes()->value; 
    } else {
        $cloud_forecast = $weather->clouds->attributes()->all;
    }
    $temperature_forecast = number_format((float)$weather->temperature->attributes()->day, 1, '.', '');
    $humidity_forecast = $weather->humidity->attributes()->value;
        
    mysql_query('UPDATE forecast SET wind_val="' . mysql_real_escape_string($wind_val_forecast) . '",    
    wind_name="' . mysql_real_escape_string($wind_name_forecast) . '",
    weather_icon_value="' . mysql_real_escape_string($weather_icon_value_forecast) . '",
    weather_icon_name="' . mysql_real_escape_string($weather_icon_name_forecast) . '",
    precipitation="' . mysql_real_escape_string($precipitation_forecast) . '",
    cloud="' . mysql_real_escape_string($cloud_forecast) . '",
    temperature="' . mysql_real_escape_string($temperature_forecast) . '",
    humidity="' . mysql_real_escape_string($humidity_forecast) . '"
    WHERE id=' . $i . '') or die(mysql_error());    
}
mysql_close();
exit;
?>
