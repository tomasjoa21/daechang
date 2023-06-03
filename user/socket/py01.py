import datetime

a = str(10)
print(f'1. str(10) = {a}, type(a) = {type(a)}')

now = datetime.datetime.now()
result = str(now)
# result = now[0:7]

print(result[0:19])
