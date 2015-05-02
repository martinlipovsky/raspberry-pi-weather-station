<?php
include("./config/config.php"); 

if (empty($_POST['day_min'])) {
    $day_min = date('Y-m-d', strtotime('-1 day'));
} else {
    $day_min = mysql_real_escape_string($_POST['day_min']);        
}
if (empty($_POST['day_max'])) {
    $day_max = date('Y-m-d');
} else {
    $day_max = mysql_real_escape_string($_POST['day_max']);        
}

if (empty($_POST['outtemp'])) {
$outtemp = 'on';
} elseif ($_POST['outtemp'] == "off") {
$outtemp = 'off';
} else {
$outtemp = $_POST['outtemp'];
}

if (empty($_POST['hum'])) {
$hum = 'on';
} elseif ($_POST['hum'] == "off") {
$hum = 'off';
} else {
$hum = $_POST['hum'];
}
?>
<script type="text/javascript">
<!--
//Graph data
    var chartData = generatechartData();
    function generatechartData() {
            var chartData = [];      
                chartData.push(
                <?php                  	    
                $sql_chart = mysql_query("SELECT * FROM temperature WHERE date BETWEEN CAST('$day_min' AS DATE) AND (CAST('$day_max' AS DATE) + INTERVAL 1 DAY) ORDER BY date ASC") or die(mysql_error());
                $lastrow = mysql_num_rows($sql_chart);
                $j = 0;
                while($result_chart = mysql_fetch_assoc($sql_chart)){ 
                    $temperature_chart = $result_chart['temperature'];
                    $hum_chart = $result_chart['humidity'];
                    $chart_date_str = strtotime($result_chart['date']);
                    $chart_date = date("Y-m-d H:i", $chart_date_str);
                    echo("{");
                    echo 'date: "' . $chart_date . '",';
                   	if ($outtemp == "on") {
                        echo 'temp: "' . $temperature_chart . '",';
                    }
                    if ($hum == "on") {
                        echo 'hum: "' . $hum_chart . '"';
                    }                   
                    if ($lastrow == ++$j) {
                        echo '}';
                    } else {
                        echo '},';
                    }                   
                }
                mysql_close();
                ?>
                );            
            return chartData;
        }      
        var chart = AmCharts.makeChart("chartdiv", {
            "theme": "none",
            "color": "#2E2E2E",	
            "type": "serial",
        		"autoMargins": false,
        		"marginLeft":8,
        		"marginRight":8,
        		"marginTop":10,
        		"marginBottom":26,
            "pathToImages": "http://www.amcharts.com/lib/3/images/",
            "dataProvider": chartData,
            "dataDateFormat": "YYYY-MM-DD JJ:NN",            
            "valueAxes": [{
                "id":"v1",
                "axisColor": "#8CC152",
                "gridAlpha": 0,
                "axisAlpha": 1,
                "inside": true,
                "unit": " <?php echo $unit_val ?>",
                "position": "left"
            },
            {
                "id":"v2",
                "axisColor": "#E9573F",
                "gridAlpha": 0,
                "axisAlpha": 1,
                "inside": true,
                "unit": " %",
                "position": "right"
            }],
            "graphs": [{
                "valueAxis": "v1",
                "balloonText": "[[category]]<br><b>Real temp: [[value]]<?php echo $unit_val ?></b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletBorderColor": "#2E2E2E",
                "hideBulletsCount": 50,
                "lineThickness": 2,
                "lineColor": "#8CC152",
                "negativeLineColor": "#67b7dc",
                "valueField": "temp"
            },
            {
                "valueAxis": "v2",
                "balloonText": "[[category]]<br><b>Humidity: [[value]]%</b>",
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletBorderColor": "#2E2E2E",
                "hideBulletsCount": 50,
                "lineThickness": 2,
                "lineColor": "#E9573F",
                "negativeLineColor": "#67b7dc",
                "valueField": "hum"
            }],
            "chartScrollbar": {
            },
            "chartCursor": {
                "categoryBalloonDateFormat": "YYYY-MM-DD JJ:NN"
            },
            "categoryField": "date",
            "categoryAxis": {
                "minPeriod": "mm",
                "parseDates": true,
                "axisAlpha": 0,
                "minHorizontalGap":60
            }       
        });               
        
        chart.addListener("dataUpdated", zoomChart);
        zoomChart();
        
        function zoomChart(){
          if(chart){
            if(chart.zoomToIndexes){
              chart.zoomToIndexes(10, chartData.length - 1);
            }
          }
        }    
-->
</script>