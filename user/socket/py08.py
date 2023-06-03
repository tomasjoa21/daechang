import psycopg2
import json

# PostgreSQL에 접속합니다.
conn = psycopg2.connect(
    host="localhost",
    database="daechang_www",
    user="postgres",
    password="super@ingglobal*"
)

# 커서를 생성합니다.
cur = conn.cursor()

lst = []    # 최종 변수 list
lst.append('apple')
for i in range(1, 500):
    lst.append(i)

print (len(lst))
print ('\n------------------------------------')
print(lst[0:16],'...')
print(lst[20:36],'...')
print(lst[490:500])

# list -> json 
lst_json = json.dumps(lst)

# 새로운 데이터를 입력합니다. 따옴표 이슈가 있네요.
# cur.execute('INSERT INTO "g5_1_socket" ("sck_dt", "sck_ip", "sck_port", "sck_value") VALUES (\'2023-04-17 12:12:14\', \'172.17.0.2\', \'30480\', \'value4\')')
# cur.execute("INSERT INTO g5_1_socket (sck_dt, sck_ip, sck_port, sck_value) VALUES ('2023-04-17 12:12:14', '172.17.0.2', '30480', 'value4')")
cur.execute("INSERT INTO g5_1_socket (sck_dt, sck_ip, sck_port, sck_value) VALUES (%s, %s, %s, %s)", ("now()", "172.17.0.2", "30480", lst_json))

# 변경사항을 커밋합니다.
conn.commit()

# 커넥션과 커서를 닫습니다.
cur.close()
conn.close()