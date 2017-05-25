import glob
import string
import os

base_dir = '/data/southeast/PERMANENT/cellhd/'
vert_classes = ['amph',  'aves',  'mamm',  'rept']

file_list = glob.glob(base_dir + "/d_*")
for b in file_list:
    #print b
    grass_name = string.split( b, "/")[5]
    print grass_name
    grass_cmd = "r.in.gdal input=%s output=%s" % (b, grass_name)
    #print grass_cmd
    #os.system(grass_cmd)
    grass_cmd2 = "cat /var/www/html/segap/grass/se_pred_color | r.colors map=%s color=rules" % grass_name
    print grass_cmd2  
    os.system(grass_cmd2)
    #break
#break
cmd = "cat /var/www/html/segap/grass/se_pred_color | r.colors map=d_aacss color=rules"


