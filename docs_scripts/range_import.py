import glob
import string
import os

cmd = 'ogr2ogr -f "PostgreSQL" -fieldTypeToString all PG:dbname=segap_ranges '
#print cmd
x = 0
mylist = glob.glob('/tmp/ranges/*.shp')

for a in mylist:
    cmd2 = cmd + a
    print cmd2
    os.system(cmd2)
    x = x + 1
    
print x