#Raspberry pi weather station with openweathermap.org data
*Received 433mhz signal from weather station with temperature and humidity data*

##Basic Usage
What you need:

  * Raspberry pi
  * 433MHz superheterodyne receiver
  * Source codes  

Final result and comments on web site: http://www.cgsteps.com/weather/
  
##Setup
  * use setup.sql file to create tables
  * /config/config.php - setup mysql database and openweathermap.org weather
  * /raspberry pi/temperature.cpp - setup mysql database and compile it with this: g++ -Wall -o temperature temperature.cpp -lwiringPi -lmysqlclient
  * use cron for compiled temperature file (I uploaded data to server every 15min)
  * use cron for openweather.php (Upload data to server - quicker way as download this data every time. I uploaded data to server every 15min)
  
## Credits
  * 1st raspberry pi source code and tutorial, How to obtain an initial waveform - http://rayshobby.net/reverse-engineer-wireless-temperature-humidity-rain-sensors-part-1/
  * Weather Icons 1.3 - http://erikflowers.github.io/weather-icons
  * Bootstrap - http://getbootstrap.com/
  * Font-Awesome - http://fortawesome.github.io/Font-Awesome/
  
## License
Raspberry pi weather station is licensed under the MIT license. (http://opensource.org/licenses/MIT)