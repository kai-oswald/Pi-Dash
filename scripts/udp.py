#!/usr/bin/python
import socket
import requests
import json
import sys

udp_ip = '192.168.1.1'
udp_port = 8888
buffer_size = 1024

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
status = False
def getStatus():
    return status

def startServer(udp_ip, udp_port, buffer_size):
    sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    sock.bind((udp_ip, udp_port))
    print("Opened UDP socket!")
    status = True
    while True:
        recvdata, addr = sock.recvfrom(buffer_size)
        print("Received data via UDP: %s" %recvdata)
  
        url = "http://localhost/api/cart"
        #data = {'senderid': recvdata[:1]}  
        recvdata = recvdata[:1]
        headers = {'Content-type': 'application/json', 'Accept': 'text/plain'}
        r = requests.post(url, data={}, headers=headers)
        if r.status_code == 200:
            print("Enter data via REST: Sucess: %i" %r.status_code)
        else:
            print("Enter data via REST, Error: %i" %r.status_code)

def stopServer():
    status = False
    sock.close()

if __name__ == '__main__':
    startServer(udp_ip, udp_port, buffer_size)
