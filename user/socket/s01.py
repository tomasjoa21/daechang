import socket
import threading

# 호스트와 포트 지정
host = ''
port = 30480

# 소켓 생성
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

# 소켓에 호스트와 포트 바인딩
try:
    s.bind((host, port))
except socket.error as msg:
    s.close()
    print ('Bind fallido. Error Code : ' + str(msg[0]) + ' Message ' + msg[1])
    sys.exit()

# 클라이언트의 연결 요청을 기다림
s.listen()

clients = []

def handle_client(client_socket, addr):
    while True:
        # 클라이언트가 보낸 데이터 수신
        data = client_socket.recv(1024)
        if not data:
            break
        
        # 수신한 데이터 출력
        # print(f"Received data from {addr}: {data.decode()}")
        print(f"Received data from {addr}: {data}")

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