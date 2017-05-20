#include <ESP8266WiFi.h>
#define ID "1"
#define SSID "wlantest"
#define PASSWORD "testwlan"
#define IP "192.168.1.1"
#define PORT 5005
#define LED_RED 5
#define LED_GREEN 4
#define SWITCH 0

void setup() {
  pinMode(LED_RED, OUTPUT);
  pinMode(LED_GREEN, OUTPUT);
  pinMode(SWITCH, INPUT_PULLUP);
  Serial.begin(115200);
  Serial.println("Starting NodeMCU");

  WiFi.begin(SSID, PASSWORD);

  Serial.println("Connecting to defined Wlan Network");
  while (WiFi.status() != WL_CONNECTED)
  {
    delay(500);
    Serial.print(".");
  }
  Serial.println();

  Serial.print("Connected, IP address: ");
  Serial.println(WiFi.localIP());
  digitalWrite(LED_GREEN, HIGH);
  delay(1000);
  digitalWrite(LED_GREEN, LOW);

}
void loop() {
  if (!digitalRead(SWITCH))
  {
    //Button is pressed
    Serial.println("Button was pressed, initializing tcp...");
    WiFiClient tcpClient;
    if (tcpClient.connect(IP, PORT)) {
      const unsigned char data[] = ID;
      tcpClient.write(data, sizeof(data));
      Serial.println("Sendind package...");
      delay(1000);

      String line;
      while (tcpClient.available()) {
        line = tcpClient.readStringUntil('\r');
      }

      if (line == "200") {
        //Do Something that shows that it was successfully
        Serial.println("Success");

        digitalWrite(LED_GREEN, HIGH);
        delay(5000);
        digitalWrite(LED_GREEN, LOW);
      } else {
        //Do Something that shows that it was NOT successfully!
        Serial.println("An error occured");

        digitalWrite(LED_RED, HIGH);
        delay(5000);
        digitalWrite(LED_RED, LOW);
      }
    }
  }
}
