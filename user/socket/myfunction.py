from time import sleep

# =============================================================================
# 클라이언트 함수들
# =============================================================================
def get_int_1word(val):
  # 1word = 2bytes
  # int = 1234
  b1 = format(val,'016b')
  # print(b)
  c1 = b1[:8]
  c2 = b1[8:]
  # print(b1,'-',b2)
  d1 = int(c1,2)
  d2 = int(c2,2)
  return [d2,d1]

def get_int_2word(val):
  # 2words = 4bytes
  # dint1 = 123456789
  dint2 = format(val,'032b')
  # print(dint2)
  dint3 = dint2[:8]
  dint4 = dint2[8:16]
  dint5 = dint2[16:24]
  dint6 = dint2[24:32]
  # print(dint6,'-',dint5,'-',dint4,'-',dint3)
  dint7 = int(dint3,2)
  dint8 = int(dint4,2)
  dint9 = int(dint5,2)
  dint10 = int(dint6,2)
  # print(dint10,'-',dint9,'-',dint8,'-',dint7)
  return [dint10,dint9,dint8,dint7]

def get_str_ascii(str):
  # A=65(1byte), B=66(1byte)...
  # str1 = 65
  # print(ord(str))
  if(str):
    return ord(str)
  else:
    return 32

def get_str_1word(lst):
  # 1word = 2bytes (2개 문자), ex)AB CD
  # A=65(1byte), B=66(1byte)...
  lst2 = []
  for i,v in enumerate(lst):
    # print(i, v, ord(v))
    lst2.append(get_str_ascii(v))
  # print(lst2)
  return lst2


def get_bit_1word(str):
  # bit 처리
  # 0001000000010001 0001111100011111
  # bit1 = '0001000000010001'
  bit2 = str[:8]
  bit3 = str[8:]
  bit4 = int(bit2,2)
  bit5 = int(bit3,2)
  return [bit5,bit4]

