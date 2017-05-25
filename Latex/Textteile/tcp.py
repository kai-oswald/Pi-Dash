#!/usr/bin/python
import socket
import sys
import requests
import json

tcp_ip = '192.168.1.1'
tcp_port = 5005

s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
status = False
def getStatus():
    return status

def startServer(tcp_ip, tcp_port, buffer_size):
    s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
    s.bind((tcp_ip, tcp_port))
    s.listen(1)
    print('Opened TCP socket!')
    while 1:
            conn, addr = s.accept()
            recvdata = conn.recv(buffer_size)
            if not recvdata:
                    print('Error while receiving data')
                    conn.send("400")
            else:
                    print("Received data via TCP: %s" % recvdata)
                    data = {" "}
                    recvdata = recvdata[:1]  
                    url = "http://localhost/api/cart/%s" % recvdata 
                    headers = {'Content-type': 'application/json', 
						'Accept': 'text/plain'}
                    try:
                        r = requests.post(url, data={}, headers=headers, 
						timeout=10)
                        if r.status_code == 200:
                            conn.send("200")
                            print("Enter data via REST: Success %i" 
								%r.status_code)
                        else:
                            conn.send(str(r.status_code))
                            print("Enter data via REST: Error: %i" 
								%r.status_code)
                    except requests.exceptions.RequestException as e:
                        print("Error %s" %e)

def stopServer():
    status = False
    s.close()

if __name__ == '__main__':
    startServer(tcp_ip,tcp_port, 1024)
