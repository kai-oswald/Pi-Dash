import subprocess
import socket
import MySQLdb
import requests
import json

db = MySQLdb.connect("localhost", "tobias", "tobias", "temps")
curs = db.cursor()
subprocess.call(["echo", "DB connected"]);


sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
sock.bind(('192.168.1.1',8888))
#s.listen(0)
#s.accept()
while True:
        data, addr = sock.recvfrom(1024)
        print "received message:", data
        sql = "INSERT INTO test(ID, MESSAGE) VALUES ('1','%s')" % \
                (data)

        url = "http://localhost/api/products"
        data = {'name': 'Test', 'price': 0.1}
        headers = {'Content-type': 'application/json', 'Accept': 'text/plain'}
        r = requests.post(url, data=json.dumps(data), headers=headers)
        print r.text

        try:
                curs.execute(sql)
                db.commit()
        except:
                print 'Fehler'