import socket
import datetime
from ast import literal_eval

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
        # print (recibido)

        print ('-----------------------------------------------------')
        print (datetime.datetime.now().strftime("%H:%M:%S.%f"))

        # values = bytearray(recibido)
        # print (values)
        # for i,v in enumerate(values):
        #     print(v,"(",i,") ", sep='', end='/')
        # #     if i%2!=0:
        # #         print(v2, '+', v, end='/')
        # #         v3 = v+v2
        # #         print('plus=',v3)
        # #     v2 = v
        # # print (len(recibido))
        # # print (len(recibido))
        for i,v in enumerate(recibido):
            # print(v,"(",i,") ", sep='', end='/')
            if i%2!=0:
                # print("\n", v2, '+', v, sep='', end='')
                # v3 = v+v2
                # print('=',v3,sep='')
                # print(bin(v2), '+', bin(v), sep='')
                # print(format(v2,'08b'), '+', format(v,'08b'), sep='')
                # s2 = format(v2,'08b')+format(v,'08b')
                s2 = format(v,'08b')+format(v2,'08b')
                # print("\n",'s2=',s2,sep='')
                t1 = int(s2,2)
                idx = int((i-1)/2)
                print(t1,"(",idx,") ", sep='', end='/')
            v2 = v # 두개씩 합치기 위해서 바로 이전 값 저장해 둔다.

        print ('\n------------------------------------')
    sc.close()
s.close()