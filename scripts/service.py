#!/usr/bin/python
import udp
import tcp
import time
from threading import Thread
import subprocess
from flask import Flask
app = Flask(__name__)

class udpClass(Thread):
    def __init__(self):
        Thread.__init__(self)
        self.daemon = True
        self.start()
    def run(self):
        udp.startServer('192.168.1.1',8888,1024)

class tcpClass(Thread):
    def __init__(self):
        Thread.__init__(self)
        self.daemon = True
        self.start()
    def run(self):
        tcp.startServer('192.168.1.1',5005,1024)  

class myApp(Thread):
    def __init__(self):
        Thread.__init__(self)
        self.daemon = True
        self.start()
    def run(self):
        app.run(port=8080)

global cudp
global ctcp
cudp = udpClass()
ctcp = tcpClass()

@app.route('/api/server/udp/start')
def udpstartrest():
    global cudp
    global ctcp
    if cudp.is_alive() == False:
        cudp = udpClass()
        print("test %r" %cudp.is_alive())
    server_status = '{"udp":%r,"tcp":%r}' %(cudp.is_alive(), ctcp.is_alive())
    res = app.response_class(server_status)
    res.headers["Content-Type"] = "application/json"
    return res

@app.route('/api/server/status')
def statusrest():
    global ctcp
    global cudp
    server_status = '{"udp":%r,"tcp":%r}' % (cudp.is_alive(), ctcp.is_alive())
    res = app.response_class(server_status)
    res.headers["Content-Type"] = "application/json"
    return res

@app.route('/api/server/tcp/start')
def tcpstartrest():
    global cudp
    global ctcp
    if ctcp.is_alive()==False:
        ctcp = tcpClass()
    server_status = '{"udp":%r,"tcp":%r}' % (cudp.is_alive(), ctcp.is_alive())
    res = app.response_class(server_status)
    res.headers["Content-Type"] = "application/json"
    return res


if __name__== '__main__':
    appthread = myApp()
    while True:
        pass
