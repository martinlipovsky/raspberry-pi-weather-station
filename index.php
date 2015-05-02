<?php
//include the config for the database connection
include("./config/config.php"); 

//graph config
$day_min = date('Y-m-d', strtotime('-1 day'));
$day_max = date('Y-m-d');
$outtemp = 'on';
$hum = 'on';

function tempParts($temp, $index) {
    $parts = explode('.', number_format($temp, 1));
    return $parts[$index];
}

function weather_icon($weather_icon_value) {
        //sky is clear
        if ($weather_icon_value == "01d") { $weather_icon = "wi wi-day-sunny"; }
        if ($weather_icon_value == "01n") { $weather_icon = "wi wi-night-clear"; }
                                                    
        //few clouds
        if ($weather_icon_value == "02d") { $weather_icon = "wi wi-day-sunny-overcast"; }
        if ($weather_icon_value == "02n") { $weather_icon = "wi night-partly-cloudy"; }
                                                    
        //scattered clouds
        if ($weather_icon_value == "03d") { $weather_icon = "wi wi-day-cloudy"; }
        if ($weather_icon_value == "03n") { $weather_icon = "wi wi-night-cloudy"; }
                                                    
        //broken clouds
        if ($weather_icon_value == "04d") { $weather_icon = "wi wi-cloudy"; }
        if ($weather_icon_value == "04n") { $weather_icon = "wi wi-cloudy"; }
                                                    
        //shower rain
        if ($weather_icon_value == "09d") { $weather_icon = "wi wi-showers"; }
        if ($weather_icon_value == "09n") { $weather_icon = "wi wi-showers"; }
                                                    
        //rain
        if ($weather_icon_value == "10d") { $weather_icon = "wi wi-day-showers"; }
        if ($weather_icon_value == "10n") { $weather_icon = "wi wi-night-showers"; }
                                                    
        //Thunderstorm
        if ($weather_icon_value == "10d") { $weather_icon = "wi wi-thunderstorm"; }
        if ($weather_icon_value == "10n") { $weather_icon = "wi wi-thunderstorm"; }
                                                    
        //Snow
        if ($weather_icon_value == "13d") { $weather_icon = "wi wi-snow"; }
        if ($weather_icon_value == "13n") { $weather_icon = "wi wi-snow"; }
                                                    
        //Mist
        if ($weather_icon_value == "50d") { $weather_icon = "wi wi-day-fog"; }
        if ($weather_icon_value == "50n") { $weather_icon = "wi wi-night-fog"; }
        
        return $weather_icon;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="keywords" content="Raspberry pi weather station with openweathermap.org">
    <meta name="description" content="Raspberry pi weather station with openweathermap.org">
    <meta name="author" content="www.cgsteps.com">
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <title>Raspberry pi weather station with openweathermap.org</title>
        
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css"> 
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">    
    
    <link rel="stylesheet" type="text/css" href="./css/style.css">   
    <link rel="stylesheet" type="text/css" href="./css/weather/weather-icons.css">
    
    <!--Dosis:200,400,500,600-->
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Dosis:200,400,500,600">  
</head>

<body>
<h2>Raspberry pi weather station with openweathermap.org data</h2>
<h5>Received 433mhz signal from weather station with temperature and humidity data</h5>
<?php
    //select temperature + humidity from database
	$sql = mysql_query("SELECT * FROM temperature ORDER BY date DESC LIMIT 1") or die(mysql_error());
    $result = mysql_fetch_assoc($sql);    
    $content_date_str = strtotime($result['date']);
    $content_date = date("H:i - jS F, Y", $content_date_str);	
    $temperature = $result['temperature'];
    $humidity = $result['humidity'];
    
    //select openweatherdata from database for today
   	$sql_openweather = mysql_query("SELECT * FROM openweather WHERE id=1") or die(mysql_error());
    $result_openweather = mysql_fetch_assoc($sql_openweather);
    $wind_val = $result_openweather['wind_val'];
    $wind_dir = $result_openweather['wind_dir'];
    $wind_name = $result_openweather['wind_name'];
    $weather_icon_value = $result_openweather['weather_icon_value'];
    $weather_icon_name = $result_openweather['weather_icon_name'];
    $precipitation = $result_openweather['precipitation'];
    $cloud = $result_openweather['cloud'];
    $sunrise = $result_openweather['sunrise'];
    $sunset = $result_openweather['sunset'];
 ?>   
    <div class="container">                       
        <div class="row">
            <div class="col-md-3">
                <div class="well height">
                    <div class="bg">
                        <div></div> 
                        <div></div>    
                    </div>
                    <div class="weather_icon">
                        <i class="<?php echo weather_icon($weather_icon_value) ?>"></i>
                        <span><?php echo $weather_icon_name ?></span>
                    </div>                   
                </div>                       
            </div>
            <div class="col-md-6">
                <div class="well height">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="temperature_shape">
                                <div>REAL OUTER TEMP.</div><?php echo tempParts($temperature, 0); ?><span>.<?php  echo tempParts($temperature, 1); ?></span><strong><?php echo $unit_val ?></strong>
                            </div>  
                        </div> 
                        <div class="col-md-6">                                      
                            <div class="wind_shape">                                     
                                <canvas id="wind_img" width="200" height="200"></canvas>
                                <div class="wind-sq">  
                                    <span>WIND</span>
                                    <span class="wind-val"><?php echo $wind_val ?></span>
                                    <span>m/s</span>
                                </div>                                                                 
                                <?php 
                                    if (!empty($wind_name)) {
                                        echo '<span>From</span> <strong>' . $wind_name . '</strong>';    
                                    }                                    
                                ?>                                           
                            </div>
                        </div> 
                    </div> 
                </div>
            </div>
            <div class="col-md-3">
                <div class="well height">
                    <div class="last-update">
                        Last update: <?php echo $content_date; ?>    
                    </div>
                    <div class="weather_info">                                 
                        <div>
                            Humidity <strong><?php echo $humidity ?>%</strong>
                        </div>
                        <div>
                            Precipitation 
                            <strong>
                            <?php
                            if (empty($precipitation)) { 
                            echo '0 mm'; 
                            } else {
                            echo $precipitation . ' mm';
                            }
                            ?>
                            </strong>
                        </div>
                        <div>
                            Cloud cover <strong><?php echo $cloud ?>%</strong>
                        </div>                                        
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">        
        <?php 
        //select forecast openweatherdata from database  
        $day = 0; 
       	$sql_openweather_daily = mysql_query("SELECT * FROM forecast") or die(mysql_error());
        while($sql_openweather_daily_result = mysql_fetch_assoc($sql_openweather_daily)){           
            $wind_val_forecast = $sql_openweather_daily_result['wind_val'];            
            $wind_name_forecast = $sql_openweather_daily_result['wind_name'];
            $weather_icon_value_forecast = $sql_openweather_daily_result['weather_icon_value'];
            $weather_icon_name_forecast = $sql_openweather_daily_result['weather_icon_name'];
            $precipitation_forecast = $sql_openweather_daily_result['precipitation']; 
            $cloud_forecast = $sql_openweather_daily_result['cloud'];
            $temperature_forecast = $sql_openweather_daily_result['temperature'];
            $humidity_forecast = $sql_openweather_daily_result['humidity'];
        ?>       
            <div class="col-md-3">
                <div class="well">
                    <div class="block_inline">
                        <div class="forecast">
                            Forecast for
                        <?php 
                            $days = date("jS F, Y", strtotime("now + $day day"));
                            $days_name = date("l", strtotime("now + $day day"));
                            if ($day == 0) {                                
                                echo "Today "; 
                                echo ( " - " . $days . "");   
                            } else {                                
                                echo ( " " . $days_name );
                                echo ( " - " . $days . " ");
                            }
                            $day++; 
                        ?>     
                        </div> 
                        <div class="daily_forecast_temp">
                            <?php echo tempParts($temperature_forecast, 0) . '<span>.' . tempParts($temperature_forecast, 1) . '</span><strong>' . $unit_val . '</strong>'; ?> 
                            <div class="weather_icon_forecast">
                                <i class="<?php echo weather_icon($weather_icon_value_forecast) ?>"></i></div>
                        </div>
                        <div class="daily_forecast_values"> 
                            <div class="row">
                                <div class="weather_icon_name">
                                    <strong><?php echo $weather_icon_name_forecast ?></strong>
                                </div> 
                            </div>
                            <div class="row">
                                Humidity <strong><?php echo $humidity_forecast ?>%</strong>
                            </div>
                            <div class="row">
                                Precipitation <strong>
                                <?php
                                if (empty($precipitation_forecast)) {
                                    echo '0 mm'; 
                                } else {
                                    echo $precipitation_forecast . ' mm';
                                }
                                ?>
                                </strong>
                            </div>
                            <div class="row">
                                Cloud cover <strong><?php echo $cloud_forecast ?>%</strong>
                            </div>
                            <div class="row">
                                Wind <strong><?php echo $wind_val_forecast ?>m/s</strong> from <strong><?php echo $wind_name_forecast ?></strong>                         
                            </div>
                        </div>  
                    </div>
                </div>
            </div>       
            <?php } mysql_close();?>
        </div>        
                
        <div class="row">
            <div class="col-md-12">
                <div class="well">
                    <div class="col-md-4">                                        
                        <div class="checkbox">
                            <?php
                           	    if ($outtemp == "on") {
                                    echo'<input class="outtemp_graph" type="checkbox" id="c2" name="outtemp" checked/>';
                                } else {
                                    echo'<input class="outtemp_graph" type="checkbox" id="c2" name="outtemp"/>';
                                }
                            ?>                           
                            <label for="c2"><span></span>Outer Temp.</label> 
                            <?php
                           	    if ($hum == "on") {
                                    echo'<input class="hum_graph" type="checkbox" id="c3" name="hum" checked/>';
                                } else {
                                    echo'<input class="hum_graph" type="checkbox" id="c3" name="hum"/>';
                                }
                            ?>                           
                            <label for="c3"><span></span>Humidity</label>
                        </div>                        
                    </div>
                    <div class="col-md-8">                        
                        <div class="col-md-4">                               
                            <div class="form-group">                                
                                <div class="input-group">                                  
                                  <input type="text" name="date1" id="date1" class="form-control date_min" value="<?php echo $day_min; ?>">
                                  <div class="input-group-addon"><span>From</span><i class="fa fa-calendar"></i></div>
                                </div>
                            </div> 
                        </div>
                        <div class="col-md-4"> 
                            <div class="form-group">                                
                                <div class="input-group">                                  
                                  <input type="text" name="date1" id="date1" class="form-control date_max" value="<?php echo $day_max; ?>">
                                  <div class="input-group-addon"><span>To</span><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                         </div>
                         <div class="col-md-4">
                            <div id="graph" class="btn btn-primary btn-block">Update graph <div id="preloader"><i class="fa fa-spinner fa-spin"></i></div></div>                                                            
                         </div>                       
                    </div>
                    <div id="chartdiv"></div>                      
                </div>
            </div>        
        </div>         
    </div>

    <!-- Latest JS -->
    <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script> 
    <script type="text/javascript" src="http://www.amcharts.com/lib/3/amcharts.js"></script>
    <script type="text/javascript" src="http://www.amcharts.com/lib/3/serial.js"></script>
    <script type="text/javascript" src="http://www.amcharts.com/lib/3/themes/none.js"></script>   
   
    <script type="text/javascript">
    //Wind circle
    var canvas = document.getElementById('wind_img');
    var ctx = canvas.getContext('2d');
    
    var w = canvas.width;
    var h = canvas.height;
    
    var r1 = Math.min(w, h) * 0.4;    // outer radius
    var r0 = r1 - 10;                 // inner radius
    
    var n = 48;                       // number of blocks
    
    var theta = 2 * Math.PI / n;
    var phi = theta * 0.05;           // relative half-block width
    
    ctx.save();
    ctx.fillStyle = '#c0c0c0';
    ctx.translate(w / 2, h / 2);      // move to center of circle
    
    for (var i = 0; i < n; ++i) {
        ctx.beginPath();
        ctx.arc(0, 0, r0, -phi, phi);
        ctx.arc(0, 0, r1, phi, -phi, true);
        ctx.fill();
        ctx.rotate(theta);            // rotate the coordinates by one block
    }    
    ctx.restore();

    var ctx1 = canvas.getContext('2d');
        var xpos = 100;
        var ypos = 100;
        ctx1.save();
        ctx1.translate(xpos, ypos);
        ctx1.rotate(<?php echo $wind_dir ?> * Math.PI / 180); // rotate by 47 degrees and convert to radians
        ctx1.translate(-xpos, -ypos);
        ctx1.font="20px Dosis";
        ctx1.fillStyle = '#fea43c';
        ctx1.fillText("▼",100,15);
    ctx1.restore();

    //Preloader icon
    $('#preloader').hide();	
    
    //Load graph data        
    function loadgraph() {
        $('#preloader').show();	
        var date_min = $( ".date_min" ).val();
        var date_max = $( ".date_max" ).val();
            
        if ( $(".outtemp_graph").prop( "checked" ) ) {
            var outtemp_graph = "on";   
        } else {
            var outtemp_graph = "off";   
        }
        if ( $(".hum_graph").prop( "checked" ) ) {
            var hum_graph = "on";   
        } else {
            var hum_graph = "off";   
        }             
            
        var dataString = 'outtemp=' + outtemp_graph +'&hum=' + hum_graph + '&day_min=' + date_min + '&day_max=' + date_max;
            
        $.ajax
        ({
        type: "POST",
        url: "graph_data.php",
        data: dataString,
        cache: false,        
        success: function(graphdata)
        { 
            $('#chartdiv').html(graphdata); 
            $('#preloader').hide();	   
        }
        });            
    };
    
    //Change graph data
    $( "#graph" ).click(function() {
        loadgraph();
        if ($('body').css('background-color') == "rgb(242, 242, 242)") {            
            chart.color = "#2E2E2E";
            chart.validateData();     
        } else {
            setTimeout(
                function() 
                  {
                    chart.color = "#FFF";
                    chart.validateData();  
                  }, 500);   
        }          
    });  
    
    //BG color change
    $( ".bg div:first-child" ).click(function() {
        $('body').css('background', $(this).css('background-color'));  
        $('body').css('color', "#303030"); 
        $('.well').css('background', "#FFF");   
        $('.well').css('border', "1px solid #e3e3e3");
        $('.checkbox').css('color', "#2E2E2E"); 
        chart.color = "#2E2E2E";
        chart.validateData();             	   
    });   
    $( ".bg div:last-child" ).click(function() {
        $('body').css('background', $(this).css('background-color'));
        $('body').css('color', "#FFF");
        $('.well').css('background', "transparent");  
        $('.well').css('border', "1px solid #4D4D4D"); 
        $('.checkbox').css('color', "#FFF"); 
        chart.color = "#FFF";
        chart.validateData();             	   
    });  
    
    loadgraph();             			
    </script>  
</body>
</html>