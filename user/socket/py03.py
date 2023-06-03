# test for file make and dir make as well as delete all dir including paths.
from datetime import datetime as dt
import os
import shutil

file_path = "data/Combine/History/test.txt"
dir_path = "data/Combine/History"

def delete(path):
    if os.path.isfile(path):
        os.remove(path)
    elif os.path.isdir(path):
        shutil.rmtree(path)
    else:
        raise ValueError("해당 경로를 확인해주세요")

# delete(file_path)
# delete(dir_path)
print(dt)
