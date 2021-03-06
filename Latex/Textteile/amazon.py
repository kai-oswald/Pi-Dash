#!/usr/bin/python
import socket
import time
import struct
import binascii
import requests
import json
 
def startServer():
    amazons = socket.socket(socket.AF_PACKET, 
		socket.SOCK_RAW, socket.htons(0x0003))
    a = " "
    b = " " 
    c = 0
    print "Amazon listener started"
    while True:
        packet = amazons.recvfrom(2048)
        ethernet_header = packet[0][0:14]
        ethernet_detailed = struct.unpack("!6s6s2s", ethernet_header)
        arp_header = packet[0][14:42]
        arp_detailed = struct.unpack("2s2s1s1s2s6s4s6s4s", arp_header)
        # skip non-ARP packets
        ethertype = ethernet_detailed[2]
        if ethertype != '\x08\x06' or socket.inet_ntoa(arp_detailed[6]) 
			!= "192.168.1.144":
            continue
        else:
            c = c + 1        
            if c % 2 == 0 and c != 0:
                print "Enter to DB"
                url = "http://localhost/api/cart/1" #1 is demo  senderid
                headers = {'Content-type': 'application/json', 
					'Accept': 'text/plain'}
                try:    
                    r = requests.post(url, data={}, headers=headers, timeout=10)
                    if r.status_code == 200:
                        print("Enter data via REST: Success %i" %r.status_code)
                    else:
                        print("Enter data via REST: Error: %i" %r.status_code)
                except requests.exceptions.RequestException as e:
                    print("Error %s" %e)

if __name__ == '__main__':
    startServer()
