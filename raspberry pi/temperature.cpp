/* Convert RF signal into bits (temperature sensor version) 
 * Written by : Ray Wang (Rayshobby LLC)
 * http://rayshobby.net/?p=8827
 * Update: adapted to RPi using WiringPi 
 *
 *
 * Updated by cgsteps.com
 * http://www.cgsteps.com/weather/
 * compile with this: g++ -Wall -o temperature temperature.cpp -lwiringPi -lmysqlclient
 */
#include <wiringPi.h>
#include <stdlib.h>
#include <stdio.h>
#include <mysql/mysql.h>
#include <iostream>
#include <string.h>
#include <ctime>

#define SERVER "localhost" //mysql address
#define USER "XXX" //mysql user name
#define PASSWORD "XXX" //mysql password
#define DATABASE "XXX" //mysql database name

//----------------------------------------------------------

#define RING_BUFFER_SIZE 500
#define SYNC_LENGTH 3900
#define SEP_LENGTH 500
#define BIT1_LENGTH 2000
#define BIT0_LENGTH 1000

#define DATA_PIN 7  // wiringPi GPIO 2 (P1.13)

unsigned long timings[RING_BUFFER_SIZE];
unsigned int syncIndex1 = 0;
unsigned int syncIndex2 = 0;
bool received = false;
int tolerance = 700;
std::string channel_id = "1000";

//---------------------------------------------------------

bool isSync(unsigned int idx){
	unsigned long t0 = timings[(idx+RING_BUFFER_SIZE-1)%RING_BUFFER_SIZE];
	unsigned long t1 = timings[idx];
    
	if((t0 > (SEP_LENGTH-200)) && (t0 < (SEP_LENGTH+200)) && (t1 > (SYNC_LENGTH-1000)) && (t1 < (SYNC_LENGTH+1000)) && (digitalRead(DATA_PIN) == HIGH)){
        return true;        
	}
	return false;
}
void handler(){
	static unsigned long duration = 0;
	static unsigned long lastTime = 0;
	static unsigned int ringIndex = 0;
	static unsigned int syncCount = 0;

	if(received == true){
		return;
	}

	long time = micros();
	duration = time - lastTime;
	lastTime = time;

	ringIndex = (ringIndex+1) % RING_BUFFER_SIZE;
	timings[ringIndex] = duration;

	if(isSync(ringIndex)){
		syncCount++;
		if(syncCount == 1){
			syncIndex1 = (ringIndex+1) % RING_BUFFER_SIZE;
		}
		else if(syncCount == 2){		  
			syncCount = 0;
			syncIndex2 = (ringIndex+1) % RING_BUFFER_SIZE;
			unsigned int changeCount = (syncIndex2 < syncIndex1) ? (syncIndex2 + RING_BUFFER_SIZE - syncIndex1) : (syncIndex2 - syncIndex1);
            
            //if(changeCount == 74){
            //    printf("%d", changeCount);
            //}
            if(changeCount != 74){
				received = false;
				syncIndex1 = 0;
				syncIndex2 = 0;
			}
			else {	            
				received = true;
			}
		}
	}
}

int main(int argc, char *argv[]){ 

    if(wiringPiSetup() == -1){
		printf("no wiring pi detected\n");
		return 0;
	}
	wiringPiISR(DATA_PIN,INT_EDGE_BOTH,&handler);

	while(true){
		if(received == true){            
			system("/usr/local/bin/gpio edge 2 none");
            /*
			//Print binary numbers 0 1
            for(unsigned int i = syncIndex1; i != syncIndex2; i = (i+2)%RING_BUFFER_SIZE){
				unsigned long t0 = timings[i],t1 = timings[(i+1)%RING_BUFFER_SIZE];
				if(t0>(SEP_LENGTH-100) && t0<(SEP_LENGTH+100)){
					if(t1>(BIT1_LENGTH-tolerance) && t1<(BIT1_LENGTH+tolerance)){
						printf("1");
					} else if(t1>(BIT0_LENGTH-tolerance) && t1 < (BIT0_LENGTH+tolerance)){
						printf("0");
					} else {
						printf(" SYNC");
					}
				} else {
					printf("%lu",t0);
				}
			}
			printf(" - ");            
            */
			//Channel
            std::string channel;
            channel.reserve(10);                      
            for(unsigned int i = (syncIndex1+16)%RING_BUFFER_SIZE; i!=(syncIndex1+24)%RING_BUFFER_SIZE; i=(i+2)%RING_BUFFER_SIZE){
				unsigned long t0 = timings[i],t1 = timings[(i+1)%RING_BUFFER_SIZE];
				if(t0>(SEP_LENGTH-200) && t0<(SEP_LENGTH+200)){
					if(t1>(BIT1_LENGTH-tolerance) && t1<(BIT1_LENGTH+tolerance)){						
                        channel += '1';
					} else if(t1>(BIT0_LENGTH-tolerance) && t1 < (BIT0_LENGTH+tolerance)){						
                        channel += '0';
					}
				}
			}            
            //printf(" | %s | ",channel.c_str());	           
            
            if (channel_id == channel){           
    			unsigned int temp = 0;
    			bool negative = false;
    			bool fail = false;
    			for(unsigned int i =(syncIndex1+24)%RING_BUFFER_SIZE;
    				i!=(syncIndex1+48)%RING_BUFFER_SIZE; i=(i+2)%RING_BUFFER_SIZE){
    				unsigned int t0 = timings[i], t1 = timings[(i+1)%RING_BUFFER_SIZE];
    				if(t0>(SEP_LENGTH-200) && t0<(SEP_LENGTH+200)){
    					if(t1>(BIT1_LENGTH-tolerance) && t1<(BIT1_LENGTH+tolerance)){
    						if( i== (syncIndex1+24)%RING_BUFFER_SIZE){
    							negative = true;                                
    						}
    						temp = (temp << 1) + 1;                        
    					} else if(t1>(BIT0_LENGTH-tolerance) && t1<(BIT0_LENGTH+tolerance)){
    						temp = (temp << 1) + 0;                        
    					} else {
    						printf("not one or zero temp: %d\n",t1);
    						fail = true;
    					}
    				} else {
    					printf("wrong seporation length temp: %d\n",t0);
    					fail = true;
    				}
    			}        
                
    			unsigned int hum = 0;		
    			for(unsigned int j =(syncIndex1+58)%RING_BUFFER_SIZE; 
    				j!=(syncIndex1+72)%RING_BUFFER_SIZE; j=(j+2)%RING_BUFFER_SIZE){
    				unsigned int t0 = timings[j], t1 = timings[(j+1)%RING_BUFFER_SIZE];               
                
    				if(t0>(SEP_LENGTH-200) && t0<(SEP_LENGTH+200)){
    					if(t1>(BIT1_LENGTH-tolerance) && t1<(BIT1_LENGTH+tolerance)){
    						hum = (hum << 1) + 1;                        
    					} else if(t1>(BIT0_LENGTH-tolerance) && t1<(BIT0_LENGTH+tolerance)){
    						hum = (hum << 1) + 0;                        
    					} else {
    						printf("not one or zero hum: %d\n",t1);
    						fail = true;
    					}
    				} else {
    					printf("wrong seporation length hum: %d\n",t0);
    					fail = true;
    				}
    			}
                
    			if(!fail){                               
                    double temperature_decimal;
                    
    				if(negative){ 
    				    double temperature = (temp);
                        temperature_decimal = ((4096 - temperature)/10); 
                        temperature_decimal = -temperature_decimal;                                                                    
    				} else {   
    				    double temperature = (temp); 
                        temperature_decimal = ((temperature)/10);                                                                                           
    				}                                             
                    	                
                    //printf("%.1f°C %d%%\n",temperature_decimal,hum);
                    
                    //START MYSQL ----------------------------------------------------------                
                    MYSQL *connect; 
                    connect=mysql_init(NULL); 
                    char finval[1024];
                    std::string url;  
                
                    if(!connect)
                    {
                        fprintf(stderr,"MySQL Initialization Failed");
                        return 1;
                    }
                         
                    connect=mysql_real_connect(connect,SERVER,USER,PASSWORD,DATABASE,3309,NULL,0);
                         
                    //if(connect){
                    //    printf("Connection Succeeded\n");
                    //}
                    //else{
                    //    printf("Connection Failed!\n");
                    //}                  
                
                    url = "INSERT INTO temperature (temperature, humidity) VALUES (";                
                   	    sprintf(finval,"'%.1f', ",temperature_decimal);
                       	url += finval;            
                       	sprintf(finval,"'%d')",hum);
                       	url += finval;                                   
                    mysql_query(connect, url.c_str()); 
                    mysql_close(connect);                
                    
                    exit(0);		
    			} else {
    				printf("Decoding Error.\n");
    			}
            }                
			delay(1000);
			wiringPiISR(DATA_PIN,INT_EDGE_BOTH,&handler);
			received = false;
			syncIndex1 = 0;
			syncIndex2 = 0;
 		}
	}
	exit(0);
}
