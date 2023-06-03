# import data.p1677486104
# print(lst)
# print(lst[0],lst[1],lst[2],lst[3],'...')
# print(lst[350],lst[351],lst[352],lst[353],'...')
# print(lst[390],lst[391],lst[392],lst[393],lst[394],lst[395],lst[396],lst[397],'...',lst[425],lst[426],'...')
# print(lst[430],lst[431],lst[432],lst[433],'...')

import ast

# x = "['B-EXP','I-EXP','B-SUB','I-SUB','O','O']"
# x = ast.literal_eval(x)
# print(x)


# 텍스트파일 전체를 통째로 출력
f = open(f'data/1677485954', 'r')
fread = f.read()
# print("file content: ----------- ")
lst = ast.literal_eval(fread)
# lst1 = fread
# print(fread)
f.close()
# print(lst1)


# print(lst)
print(lst[0],lst[1],lst[2],lst[3],'...')
print(lst[350],lst[351],lst[352],lst[353],'...')
print(lst[390],lst[391],lst[392],lst[393],lst[394],lst[395],lst[396],lst[397],'...',lst[425],lst[426],'...')
print(lst[430],lst[431],lst[432],lst[433],'...')

