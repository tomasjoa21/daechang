# 데이터 베이스에 내용을 저장합니다. Change port to 20480
# 엑셀 참고: https://docs.google.com/spreadsheets/d/1baQOZuue_rMJ2xiY1DhqHxFDAfeefK_94llKKmPBEO8/edit?usp=sharing
import socket
import sys
import threading
from datetime import datetime, timedelta
import time
import os
import shutil
import psycopg2
import json

# 호스트와 포트 지정
host = ''
port = 20480

# 소켓 생성
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

# 소켓에 호스트와 포트 바인딩
try:
    s.bind((host, port))
except socket.error as msg:
    s.close()
    print ('Bind fallido. Error Code : ' + str(msg[0]) + ' Message ' + msg[1])
    sys.exit()

# database connection
try:
    conn = psycopg2.connect(
        host="localhost",
        database="daechang_www",
        user="postgres",
        password="super@ingglobal*"
    )
    # 커서를 생성합니다.
    cur = conn.cursor()
except Exception as e:
    print(e)

# 클라이언트의 연결 요청을 기다림
s.listen()

clients = []

def handle_client(client_socket, addr):
    while True:
        # # 클라이언트가 보낸 데이터 수신
        # data = client_socket.recv(1024)
        # if not data:
        #     break
        
        # # 수신한 데이터 출력
        # # print(f"Received data from {addr}: {data.decode()}")
        # print(f"Received data from {addr}: {data}")
        ip = addr[0]
        # print(ip)

        try:
            recibido = client_socket.recv(1024)
            if not recibido: break
            len1 = len(recibido)    # 배열의 크기
            # print (len1)
            # print (recibido)

            print ('-----------------------------------------------------')
            d2 = datetime.now().strftime('%Y-%m-%d %H:%M:%S') # 2023-02-27 11:11:11
            t2 = time.mktime(datetime.strptime(d2, '%Y-%m-%d %H:%M:%S').timetuple()) # timestamp=1677478797.23455
            t3 = int(t2) # timestamp=1677478797 (delete numbers after dot(.))
            d3 = d2[0:10]   # 2023-02-27
            d4 = d2[11:13]  # hour = 11
            d5 = datetime.now() - timedelta(days=7) # 30days ago
            d6 = datetime.now() - timedelta(minutes=3) # 3minutes ago

            # make folder(s) if not exists.
            # path0 = f'../../data/socket/{ip}/{port}'
            path0 = f'/home/daechang/www/data/socket/{ip}/{port}'
            path1 = f'{path0}/{d3}/{d4}'
            # print(path1)
            if not os.path.isdir(path1):
                os.makedirs(path1)

            print (d2, ' (bytes=', len1, ')', sep='')

            lst = []    # 최종 변수 list
            list2 = [0,0]
            list4 = [0,0,0,0]
            for i,v in enumerate(recibido):
                # print(v,"(",i,") ", sep='', end='/')
                if i<940: # 2bytes = 1word
                    # print(v,"(",i,") ", sep='', end='/')
                    idx = i%2
                    # print(" idx=", idx, end=' ')
                    list2[idx] = v   # 목적에 맞는 갯수만큼 합치기 위해서 list에 담아두고 해당 갯수가 되었을 때 처리합니다.
                    st = ''
                    if idx==1:
                        # for j,v2 in enumerate(list2):
                        for j,v2 in reversed(list(enumerate(list2))):
                            # print(j, ':', v2, end=' / ')
                            st += format(v2,'08b')
                        # print(st, sep='', end=' > ')
                        t1 = int(st,2)
                        # print(t1)
                        lst.append(t1)
                        # if i<6: # 2개를 표현하려면 byte 단위인 2배수를 써야 함
                        #     print(t1, "(",i,")", sep='')
                        # print(t1, "(",i,")", sep='')
                else:
                    # print(v,"(",i,") ", sep='', end='/')
                    idx = i%2
                    # print(" idx=", idx, end=' ')
                    list2[idx] = v   # 목적에 맞는 갯수만큼 합치기 위해서 list에 담아두고 해당 갯수가 되었을 때 처리합니다.
                    st = ''
                    if idx==1:
                        # print(v2, '+', v, sep='', end='-')
                        # print("\n", i," index", sep='')
                        # for j,v2 in enumerate(list1):
                        for j,v2 in reversed(list(enumerate(list2))):
                            # print(format(v2,'08b'), end='/')
                            st += format(v2,'08b')
                        reversed_str = st[::-1]  # 문자열 뒤집기
                        reversed_binary = int(reversed_str, 2)  # 2진수 문자열을 10진수 정수로 변환
                        st2 = bin(reversed_binary)[2:].zfill(16)  # 10진수 정수를 2진수 문자열로 변환하고 16자리로 맞추기
                        lst.append(st2)
                        # if i<943 : # 2개를 표현하려면 byte 단위인 2배수를 써야 함
                        #     print(st, "(",i,")", sep='')

            # print (lst)
            # lst[0] = ip
            print(lst[0:16],'...')
            print(lst[470:486],'...')
            # print ('\n------------------------------------')

            # create file and write.
            # f = open(f'{path0}/{d3}/{d4}/{t3}', 'w')
            # # 파일 생성
            # # data = f"{d3}/{d4}/{t3}.txt"
            # # data += f"\n{d3}/{d4}/{t3}.txt"
            # f.write(str(lst))
            # f.close() # 쓰기모드 닫기

            # write to pgSQL
            try:
                lst_json = json.dumps(lst) # list -> json (json 타입으로 바꿔야 db 입력시 no error)
                # cur.execute("INSERT INTO g5_1_socket (sck_dt, sck_ip, sck_port, sck_value) VALUES (%s, %s, %s, %s)", ("now()", ip, port, lst_json))
                cur.execute("INSERT INTO g5_1_socket (sck_dt, sck_ip, sck_port, sck_value) VALUES (%s, %s, %s, %s)", (d2, ip, port, lst_json))
                conn.commit()
            except Exception as e:
                conn.rollback()  # 이전 트랜잭션 롤백
                print(e)

            # delete folders which are past than specific days.
            entries = os.listdir(path0)
            for entry in entries:
                if entry < str(d5):
                    # print(entry)
                    shutil.rmtree(f'{path0}/{entry}')

        except ValueError:
            print('ValueError occured.')
        except Exception as e:
            print(e)

        # # 다른 클라이언트들에게 데이터 전송 -> 데이터 reply는 별도로 하지 않습니다.
        # for c in clients:
        #     if c != client_socket:
        #         c.send(data)

    # 소켓 연결 종료
    print(f"Disconnected from {addr}")
    client_socket.close()
    clients.remove(client_socket)


print(f"Listening on port {port}...")

while True:
    # 클라이언트의 연결 요청을 수락
    client_socket, addr = s.accept()
    print(f"Accepted connection from {addr}")

    # 연결된 클라이언트를 리스트에 추가
    clients.append(client_socket)

    # 클라이언트를 처리하는 스레드 생성
    client_thread = threading.Thread(target=handle_client, args=(client_socket, addr))
    client_thread.start()


# DB 커넥션과 커서를 닫습니다.
cur.close()
conn.close()