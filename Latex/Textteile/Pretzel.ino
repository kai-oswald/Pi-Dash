/*
  UDP Button Send.
  No change necessary.
*/

#define DEBUG true

#define LED 9
#define LED_WLAN 13
#define SWITCH 8
#define ID "2"
#define SSID "wlantest"
#define PASSWORD "testwlan"
#define IP "192.168.1.1"
#define PORT "8888"

#include <SoftwareSerial.h>

SoftwareSerial esp8266(11, 12); // RX, TX

void setup() {
  Serial.begin(19200);
  esp8266.begin(19200);

  pinMode(LED, OUTPUT);
  pinMode(SWITCH, INPUT_PULLUP);

  esp8266.println("AT+RST");
  esp8266.setTimeout(5000);
  if (esp8266.find("ready")) {
    Serial.println("Reset OK");
  }
  else
  {
    Serial.println("Reset Error");
  }

  if (configAP()) {
    debug("AP ready");
    digitalWrite(LED, HIGH);
    delay(1000);
    digitalWrite(LED, LOW);
  }
  if (configUDP()) {
    esp8266.setTimeout(1000);
    debug("UDP ready");
  }
  //shorter Timeout for faster wrong UPD-Comands handling
}

void loop() {
  if (!digitalRead(SWITCH))
  {
    debug("Button was pressed, sending ID");
    sendUDP(ID);
    digitalWrite(LED, HIGH);
    delay(5000);
    digitalWrite(LED, LOW);
  }
}
//---------Config ESP8266------------

boolean configAP()
{
  boolean succes = true;

  esp8266.setTimeout(1000);
  esp8266.println("AT+CWMODE=1");
  if (esp8266.find("OK")) {
    Serial.println("CWMODE OK");
  }
  else
  {
    Serial.println("CWMODE Error");
  }
  //Connect to WLAN, long Timeout
  esp8266.setTimeout(20000);
  esp8266.println("AT+CWJAP=\"" + String(SSID) + "\",\"" + String(PASSWORD) + "\"");
  if (esp8266.find("OK")) {
    Serial.println("WLAN Connect OK");
  }
  else
  {
    Serial.println("WLAN Connect Error");
  }

  return succes;
}

boolean configUDP()
{
  boolean succes = true;

  succes &= (sendCom("AT+CIPMODE=0", "OK"));
  succes &= (sendCom("AT+CIPMUX=0", "OK"));
  succes &= sendCom("AT+CIPSTART=\"UDP\",\"" + String(IP) + "\"," + String(PORT) + "", "OK"); //UDP-Server
  return succes;
}

//-------Controll ESP------

boolean sendUDP(String Msg)
{
  boolean succes = true;

  succes &= sendCom("AT+CIPSEND=" + String(Msg.length() + 2), ">");
  if (succes)
  {
    succes &= sendCom(Msg, "OK");
  }
  return succes;
}

boolean sendCom(String command, char respond[])
{
  esp8266.println(command);
  if (esp8266.findUntil(respond, "ERROR"))
  {
    return true;
  }
  else
  {
    debug("ESP SEND ERROR: " + command);
    return false;
  }
}

String sendCom(String command)
{
  esp8266.println(command);
  return esp8266.readString();
}

//------Debug Functions------
void serialDebug() {
  while (true)
  {
    if (esp8266.available())
      Serial.write(esp8266.read());
    if (Serial.available())
      esp8266.write(Serial.read());
  }
}

void debug(String Msg)
{
  if (DEBUG)
  {
    Serial.println(Msg);
  }
}
