#bin/bash
pwd
cd /data/html/wemember
#sudo git pull origin master

echo "==============================================="  

echo "deploying the web"  

sudo git add .
sudo git commit -a -m 'auto commit server update'
#git stash  
sudo git pull origin master  
sudo git push origin master
#git fetch --all  
sudo git reset --hard origin/master  

time=`date`
echo "web server pull at webserver at time: $time."
echo "================================================"
chown -R www ./
chgrp -R www ./
echo "deploy directory over"
