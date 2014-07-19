#!/bin/bash
sudo echo -n

echo "                         ____  _ ____            _     "
echo "                        |  _ \(_)  _ \  __ _ ___| |__  "
echo "                        | |_) | | | | |/ _  / __| '_ \ "
echo "                        |  __/| | |_| | (_| \__ \ | | |"
echo "                        |_|   |_|____/ \__,_|___/_| |_|"
echo
echo "------------------------------------------------------------------------------"
echo
echo -n "Would you like me to setup some software needed for PiDash? [y/n] "
read -n 1 q
case $q in
    y)
        echo;;
    *)
        echo
        echo "Alright, call me up when you want to use PiDash"
        exit;;
esac
echo
CWD=`pwd`

echo "First of all, I'm going to install stuff like unzip, screen, omxplayer, wget, raspi2png and youtube-dl..."
sudo apt-get -y install unzip screen omxplayer wget youtube-dl git libpng12-dev make python-pexpect ffmpeg openvpn > /dev/null
echo "Install done, updating youtube-dl..."
sudo youtube-dl -U > /dev/null

echo "Installing raspi2png..."
cd /tmp
git clone "https://github.com/AndrewFromMelbourne/raspi2png.git" > /dev/null
cd raspi2png
make > /dev/null
cp raspi2png $CWD/assets/c
rm /tmp/raspi2png -rf
cd $CWD

echo "Downloading phpQuery..."
cd assets/php/modules
wget -O phpquery.zip https://phpquery.googlecode.com/files/phpQuery-0.9.5.386-onefile.zip > /dev/null
unzip phpquery.zip
rm phpquery.zip
mv phpQuery-onefile.php phpquery.php
cd $CWD

echo "Downloading omxplayer dbus-script..."
cd assets/php/bash
wget -O omxcontrol.sh https://raw.githubusercontent.com/popcornmix/omxplayer/master/dbuscontrol.sh > /dev/null
chmod +x omxcontrol.sh
cd $CWD
