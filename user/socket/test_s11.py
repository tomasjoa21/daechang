import socket
import datetime

s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
# s.bind((socket.gethostname(),8080))
# s.bind(('',8080))
s.bind(('',20480))
# s.listen(10)
s.listen()

while True:
    now = datetime.datetime.now()
    dt = str(now)
    c_socket, client_ip = s.accept()
    # print(f"{client_ip} 가 연결되었습니다.")
    # print("Client Socket info : \n", c_socket)
    print(c_socket)
    # c_socket.send(bytes("Hi","utf-8"))