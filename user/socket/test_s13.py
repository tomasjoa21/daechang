import socket
import datetime


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
    
        print ('-----------------------------------------------------')
        print (datetime.datetime.now().strftime("%H:%M:%S.%f"))
        # print (len(recibido))
        for i,v in enumerate(recibido):
            print(v,"(",i,") ", sep='', end='/')
        print ('\n------------------------------------')
    sc.close()
s.close()