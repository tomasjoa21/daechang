# make file with the name of date in the form of hierarchy. Delete folder which pasted more than 3days with all the sub files.
from datetime import datetime, timedelta
import time
import os
import shutil

t1 = time.time()
# print(t1)
d1 = datetime.fromtimestamp(t1)
# print(d1)
d2 = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
# print(d2)
t2 = time.mktime(datetime.strptime(d2, '%Y-%m-%d %H:%M:%S').timetuple())
# print(t2)

d3 = d2[0:10]
print(d3)
d4 = d2[5:10]
print(d4)
d5 = d2[11:13]
print(d5)
t3 = int(t2)
print(t3)
d6 = datetime.now()
d7 = datetime.now() - timedelta(days=3)
d8 = datetime.now() - timedelta(minutes=3)
print(f'current t: {d6}')
print(f'3days ago: {d7}')
print(f'3mins ago: {d8}')


path1 = f'data/{d3}/{d5}'
print(path1)
if not os.path.isdir(path1):
    os.makedirs(path1)

# create file and write.
f = open(f'data/{d3}/{d5}/{t3}.txt', 'w')
# 파일 생성
data = f"{d3}/{d5}/{t3}.txt"
data += f"\n{d3}/{d5}/{t3}.txt"
f.write(data)
f.close() # 쓰기모드 닫기

# 텍스트파일 전체를 통째로 출력
f = open(f'data/{d3}/{d5}/{t3}.txt', 'r')
fread = f.read()
print("file content: ----------- ")
print(fread)
f.close()


# delete folders which are past than specific days.
entries = os.listdir('data')
for entry in entries:
    if entry < str(d7):
        # print(entry)
        shutil.rmtree(f'data/{entry}')

# folder paths. 2023-02/02-27/00~23/1677465636 (timestamp)
# print(os.getcwd())
