#!/usr/bin/python
#Packet sniffer in python
#For Linux
 
#Packet sniffer in python
#For Linux
 
import socket
import time
import struct
import binascii
 
#create an INET, raw socket
s = socket.socket(socket.AF_PACKET, socket.SOCK_RAW, socket.htons(0x0003))
#s.bind(("192.168.1.1",3384))
a = " "
b = " " 
c = 0
# receive a packet
while True:
    packet = s.recvfrom(2048)
#    if b != " ":
#       c = c + 1
#    if c == 2 :
#        time.sleep(32)
#        print "Fertig"
#        c = 1
#        b = " "     
 
    ethernet_header = packet[0][0:14]
    ethernet_detailed = struct.unpack("!6s6s2s", ethernet_header)

    arp_header = packet[0][14:42]
    arp_detailed = struct.unpack("2s2s1s1s2s6s4s6s4s", arp_header)

    # skip non-ARP packets
    ethertype = ethernet_detailed[2]
    if ethertype != '\x08\x06' or socket.inet_ntoa(arp_detailed[6]) != "192.168.1.144":
        continue
    else:
        c = c + 1        
        if c % 2 == 0 and c != 0:
            print "DB"
    #if  socket.inet_ntoa(arp_detailed[6]) != "192.168.1.144":
    #    print "ABC"
    print "****************_ETHERNET_FRAME_****************"
    #print "Dest MAC:        ", binascii.hexlify(ethernet_detailed[0])
    #print "Source MAC:      ", binascii.hexlify(ethernet_detailed[1])
    #print "Type:            ", binascii.hexlify(ethertype)
    #print "************************************************"
    #print "******************_ARP_HEADER_******************"
    #print "Hardware type:   ", binascii.hexlify(arp_detailed[0])
    #print "Protocol type:   ", binascii.hexlify(arp_detailed[1])
    #print "Hardware size:   ", binascii.hexlify(arp_detailed[2])
    #print "Protocol size:   ", binascii.hexlify(arp_detailed[3])
    #print "Opcode:          ", binascii.hexlify(arp_detailed[4])
    #print "Source MAC:      ", binascii.hexlify(arp_detailed[5])
    #print "Source IP:       ", socket.inet_ntoa(arp_detailed[6])
    #print "Dest MAC:        ", binascii.hexlify(arp_detailed[7])
    #print "Dest IP:         ", socket.inet_ntoa(arp_detailed[8])
    #print "*************************************************\n"
    #print c
