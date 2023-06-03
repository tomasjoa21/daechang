# date and datetime testing..
from datetime import datetime
import time

# a = str(10)
# print(f'1. str(10) = {a}, type(a) = {type(a)}')

# now = datetime.datetime.now()
# result = str(now)
# # result = now[0:7]

# print(result[0:19])

t1 = time.time()
print(t1)

t2 = datetime.fromtimestamp(t1)
print(t2)

s2 = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
print(s2)

s3 = time.mktime(datetime.strptime(s2, '%Y-%m-%d %H:%M:%S').timetuple())
print(s3)

# timestamp = time.mktime(datetime.today().timetuple())
# print(timestamp)

binary_str = '0000000000010011'
reversed_str = binary_str[::-1]  # 문자열 뒤집기
reversed_binary = int(reversed_str, 2)  # 2진수 문자열을 10진수 정수로 변환
result = bin(reversed_binary)[2:].zfill(16)  # 10진수 정수를 2진수 문자열로 변환하고 16자리로 맞추기
print(result)  # '1100100000000000' 출력
