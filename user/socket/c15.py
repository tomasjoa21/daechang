# 소켓을 사용하기 위해서는 socket을 import해야 한다.
import socket
from time import sleep
import myfunction
import random
import pprint

# 로컬은 127.0.0.1의 ip로 접속한다.
HOST = '127.0.0.1'
PORT = 20480
# 소켓을 만든다.
client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
# connect함수로 접속을 한다.
client_socket.connect((HOST, PORT))

def get_increment(int,idx,inc=0):
  # 값을 하나씩 증가시키기
  global sets
  if int==29999:
    sets[idx] = 0
  else:
    if inc==1:
      sets[idx] = sets[idx]+1
    else:
      sets[idx] = sets[idx]
  return sets[idx]

sets = []
# 각 리스트에 0부터 29999까지의 무작위 숫자 10개 추가
for i in range(500):
  sets.append(random.randint(29900, 29999))
# print(sets)
# sets[1] = sets[1]+1
# print(sets[1])
# exit()

# data send to server
for idx in range(0,10000000000):
  # if idx>10:
  #   break

  my_bytes = bytearray()
  # my_bytes.append(1234)
  # my_bytes.append(5678)

  # 2byte 작은숫자 350개
  # 2bytes(700bytes) = 1word(350words)
  for i in range(0,470): # 350개
    if i<6:
      if idx%3 == 2:  # increase by 1 for multiple of 3
        val1 = get_increment(sets[i],i,1)
      else:
        val1 = get_increment(sets[i],i)
    elif i>=20 and i<50:
      if i == 20 or i == 25 or i == 30 or i == 35 or i == 40 or i == 45:  # Jig number
        val1 = random.randint(1, 2)
      else:
        # if idx%20 == 2: # 20 초에 1개씩 생산, production per set seconds.
        if idx%3 == 2: # 20 초에 1개씩 생산, production per set seconds. << ---------------------------------------
          val1 = get_increment(sets[i],i,1)
        else:
          val1 = get_increment(sets[i],i)
    else:
      val1 = 0
    # print(i, val1, '--------------')
    int1 = myfunction.get_int_1word(val1)
    # print(int1)
    for i1,v1 in enumerate(int1):
      # print(v1)
      my_bytes.append(v1) # list[0,1]
  # pprint.pprint(my_bytes)
  # print(len(my_bytes),'-----------------------------------------------')
  # print(i)
  # exit()

  # bit 처리 - 30개
  # 0001000000010001
  # 2bytes(60bytes) = 1word(30words)
  for i in range(470,500): # 30개
    # print(i)
    if i==498:
      val1 = '0001000000010001'
    elif i==497:
      val1 = '0001111100011111'
    elif i<476:
      ran1 = random.randint(1, 65535)
      # ran1 = random.randint(1, 512)
      if idx%19 == 0:
        val1 = format(ran1,'016b')[0:9]+'0000000'
      elif idx%29 == 0:
        val1 = format(ran1,'016b')[0:9]+'0000000'
      elif idx%59 == 0:
        val1 = format(ran1,'016b')[0:9]+'0000000'
      else:
        val1 = '0000000000000000'
    else:
      val1 = '0000000000000000'
    # print(i, val1, '--------------')
    int1 = myfunction.get_bit_1word(val1)
    # print(i, int1)
    for i1,v1 in enumerate(int1):
      # print(v1)
      my_bytes.append(v1) # list[0,1]
  # pprint.pprint(my_bytes)
  # print(len(my_bytes),'-----------------------------------------------')
  # print(i)
  # exit()

  client_socket.sendall(my_bytes)  # client_socket.send(y)
  # print(my_bytes)
  print(idx)
  sleep(1)
      
client_socket.close()