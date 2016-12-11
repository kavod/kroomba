#!/bin/bash
cd $1

cd ../node/
node ./node_modules/dorita980/bin/getpassword.js "$2" 


#sudo npm install node-dash-button

rm /tmp/kroomba_dep
