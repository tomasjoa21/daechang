# 소켓을 사용하기 위해서는 socket을 import해야 한다.
import socket
from time import sleep
import myfunction
import random
import pprint

# 로컬은 127.0.0.1의 ip로 접속한다.
HOST = '127.0.0.1'
PORT = 30480
# 소켓을 만든다.
client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
# connect함수로 접속을 한다.
client_socket.connect((HOST, PORT))

# data send to server
for idx in range(0,10000000000):
  # if idx>10:
  #   break

  my_bytes = bytearray()
  my_bytes.append(idx)
  my_bytes.append(34)
  my_bytes.append(56)

  client_socket.sendall(my_bytes)  # client_socket.send(y)
  print(idx)
  sleep(1)
      
client_socket.close()