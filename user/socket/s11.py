# 500 워드를 받아보자. (1000byte) 배열을 받아보면 실제로는 bytes 단위인 1000개가 찍힘
import socket
from datetime import datetime, timedelta
import time
import os
import shutil

HOST = ''
PORT = 30480

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

while True:
    sc, addr = s.accept()
    # print(addr[0]) #127.0.0.1
    ip = addr[0]
    print(ip)

    while True:
        try:
            recibido = sc.recv(1024)
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
            d5 = datetime.now() - timedelta(days=3) # 3days ago
            d6 = datetime.now() - timedelta(minutes=3) # 3minutes ago

            # make folder(s) if not exists.
            path0 = f'../../data/socket/{ip}/{PORT}'
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
                if i<700: # 2bytes = 1word
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
                elif i<860: # 4bytes = 2word
                    # print(v,"(",i,") ", sep='', end='/')
                    idx = i%4
                    # print(" idx=", idx, end=' ')
                    list4[idx] = v   # 목적에 맞는 갯수만큼 합치기 위해서 list에 담아두고 해당 갯수가 되었을 때 처리합니다.
                    st = ''
                    if idx==3:
                        # print(v2, '+', v, sep='', end='-')
                        # print("\n", i," index", sep='')
                        # for j,v2 in enumerate(list4):
                        for j,v2 in reversed(list(enumerate(list4))):
                            # print(j, ':', v2, end=' / ')
                            st += format(v2,'08b')
                            # # print("\n",'s2=',s2,sep='')
                            # t1 = int(s2,2)
                            # idx = int((i-1)/2)
                        # print(st, sep='', end=' > ')
                        t1 = int(st,2)
                        lst.append(t1)
                        # if i<712 : # 2개를 표현하려면 byte 단위인 4배수를 써야 함
                        #     print(t1, "(",i,")", sep='')
                        # print(t1)
                elif i<940: # 문자열 1byte (ascii) - 2byte씩 묶어서 보여달라.
                    # print(v,"(",i,") ", sep='', end='/')
                    idx = i%2
                    list2[idx] = v   # 목적에 맞는 갯수만큼 합치기 위해서 list에 담아두고 해당 갯수가 되었을 때 처리합니다.
                    st = ''
                    if idx==1:
                        # print(v2, '+', v, sep='', end='-')
                        # print("\n", i," index", sep='')
                        for j,v2 in enumerate(list2):
                        # for j,v2 in reversed(list(enumerate(list2))):
                            # print(j, '(decimal):', v2, chr(v2), end=' / ')
                            # st += chr(v2)
                            st += chr(v2) if v2!=0 else ''
                        lst.append(st)
                        # if i<866 : # 2개를 표현하려면 byte 단위인 2배수를 써야 함
                        #     print(st, "(",i,")", sep='')
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
                        lst.append(st)
                        # if i<943 : # 2개를 표현하려면 byte 단위인 2배수를 써야 함
                        #     print(st, "(",i,")", sep='')

            # print (lst)
            lst[0] = ip
            print(lst[0:16],'...')
            print(lst[20:72],'...')
            print(lst[430:446],'...')
            # print ('\n------------------------------------')

            # create file and write.
            f = open(f'{path0}/{d3}/{d4}/{t3}', 'w')
            # 파일 생성
            # data = f"{d3}/{d4}/{t3}.txt"
            # data += f"\n{d3}/{d4}/{t3}.txt"
            f.write(str(lst))
            f.close() # 쓰기모드 닫기

            # delete folders which are past than specific days.
            entries = os.listdir(path0)
            for entry in entries:
                if entry < str(d5):
                    # print(entry)
                    shutil.rmtree(f'data/plc/{entry}')

        except ValueError:
            print('ValueError occured.')
        except Exception as e:
            print(e)

    sc.close()
s.close()