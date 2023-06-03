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
  for i in range(0,350): # 350개
    if i==0:
      # val1 = 1234
      # val1 = sets[i]
      if idx%10 == 2:
        val1 = get_increment(sets[i],i,1)
      else:
        val1 = get_increment(sets[i],i)
    elif i<14:
      if idx%3 == 2:
        val1 = get_increment(sets[i],i,1)
      else:
        val1 = get_increment(sets[i],i)
      # val1 = random.randint(1111, 9999)
    elif i>=20 and i<42:
      if i%3 == 2:
        val1 = random.randint(1, 2)
      else:
        if idx%10 == 2: # 10 초에 1개씩 생산
          val1 = get_increment(sets[i],i,1)
        else:
          val1 = get_increment(sets[i],i)
      # val1 = random.randint(1111, 9999)
    elif i>=42 and i<52:
      if i%5 == 1:
        val1 = random.randint(1, 2)
      else:
        if idx%5 == 0: # 5 초에 1개씩 생산
          val1 = get_increment(sets[i],i,1)
        else:
          val1 = get_increment(sets[i],i)
      # val1 = random.randint(1111, 9999)
    elif i>=52 and i<59:
      if i%7 == 2:
        val1 = random.randint(1, 2)
      else:
        if idx%19 == 2: # 19 초에 1개씩 생산
          val1 = get_increment(sets[i],i,1)
        else:
          val1 = get_increment(sets[i],i)
      # val1 = random.randint(1111, 9999)
    elif i>=59 and i<70:
      if i%3 == 1:
        val1 = random.randint(1, 2)
      else:
        if idx%21 == 0: # 21 초에 1개씩 생산
          val1 = get_increment(sets[i],i,1)
        else:
          val1 = get_increment(sets[i],i)
      # val1 = random.randint(1111, 9999)
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

  # 4byte 큰숫자 40개
  # 4bytes(160bytes) = 2word(80words)
  for i in range(350,390): # 40개
    if i==350:
      val1 = 123456789
    elif i<360:
      val1 = random.randint(111111111, 999999999)
    else:
      val1 = 0
    # print(i, val1, '--------------')
    int1 = myfunction.get_int_2word(val1)
    # print(i, int1)
    for i1,v1 in enumerate(int1):
      # print(v1)
      my_bytes.append(v1) # list[0,1,2,3]
  # pprint.pprint(my_bytes)
  # print(len(my_bytes),'-----------------------------------------------')
  # print(i)
  # exit()

  # 문자열 1byte (ascii) - 2byte씩 묶어서 보여달라. - 80개 (2개씩 묶어서 40개)
  # 2bytes(80bytes) = 1word(40words)
  for i in range(390,470): # 80개
    # print(i)
    if i==390:
      val1 = 'A'
    elif i==391:
      val1 = 'B'
    elif i<400:
      val1 = chr(random.randrange(ord('C'), ord('Z')+1))
    else:
      val1 = ''
    # print(val1)
    # print(i%2)
    # 홀수일 때만 하단 실행
    if i%2==0:
      val2 = val1 # 두개를 합치기 위해서 미리 저장해 둠
      continue
    # print('----')
    val3 = [val2,val1]
    # print(i, val3, '--------------')
    lst1 = myfunction.get_str_1word(val3)
    # print(lst1)
    for i1,v1 in enumerate(lst1):
      # print(v1)
      my_bytes.append(v1) # list[0.1]
  # pprint.pprint(my_bytes)
  # print(len(my_bytes),'-----------------------------------------------')
  # print(i)
  # exit()

  # bit 처리 - 30개
  # 0001000000010001
  # 2bytes(60bytes) = 1word(30words)
  for i in range(470,500): # 30개
    # print(i)
    if i==470:
      val1 = '0001000000010001'
    elif i==471:
      val1 = '0001111100011111'
    elif i<484:
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