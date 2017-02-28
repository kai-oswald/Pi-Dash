#!/usr/bin/python
import udp
import tcp
from threading import Thread
import subprocess

class myClassA(Thread):
    def __init__(self):
        Thread.__init__(self)
        self.daemon = True
        self.start()
    def run(self):
        udp.startServer('192.168.1.1',8888,1024)

class myClassB(Thread):
    def __init__(self):
        Thread.__init__(self)
        self.daemon = True
        self.start()
    def run(self):
        tcp.startServer('192.168.1.1',5005,1024)  

if __name__== '__main__':
    myClassA()
    myClassB() 
    while True:
        pass
