import socket
import struct
import binascii
import datetime
import sys


HOST = ''   
PORT = 20480

s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
print ('Socket created')
 
try:
    s.bind((HOST, PORT))
except socket.error as msg:
    s.close()
    print ('Bind fallido. Error Code : ' + str(msg[0]) + ' Message ' + msg[1])
    sys.exit()

s.listen(1)

print ('Listening Socket')

# def decode_binary_string(s):
#     return ''.join(chr(int(s[i*8:i*8+8],2)) for i in range(len(s)//8))
def decode_binary_string(s):
    # n = int(s, 2)
    n = int(s)
    return n.to_bytes((n.bit_length() + 7) // 8, 'big').decode()

while True:

    sc, addr = s.accept()
    
    while True:
        
        recibido = sc.recv(2048)
        if not recibido: break
    
        b = bytearray(recibido)
        test  = binascii.hexlify(b)
    
        print ('-----------------------------------------------------')
        print (recibido)
        print (len(recibido))
        print (recibido[0])
        print ('Message received at : ', datetime.datetime.now().strftime("%H:%M:%S.%f"))
        print ('Message ', test)
        # print (decode_binary_string(test))
        print ('-------')
        result = test.decode('utf-8')
        print(result)
        print(type(result))        
    
        # print ('First attribute  ', test[0:2], ' -> ', str(int('0x', test[0:2],0)))
        # print ('Second attribute ', test[2:6], ' -> ', str(int('0x', test[2:6],0)))
    
    sc.close()

s.close()