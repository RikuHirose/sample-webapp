#!/usr/bin/env bash
# Author:      Matt Foxx Duncan <matt.duncan13@gmail.com>
#
# Description: For use with AWS elasticbeanstalk and Laravel. As part of a deployment will either skip, install,
#              or update supervisord depending on environmental variables set in EB.

updateSupervisor(){
    cp /var/app/current/supervisord.conf /etc/supervisord.conf

    sudo systemctl stop supervisord
    php /var/app/current/artisan queue:restart
    sleep 5
    sudo systemctl start supervisord
}

installSupervisor(){
    sudo easy_install pip
    pip install supervisor

    cp /var/app/current/supervisord.conf /etc/supervisord.conf
    sudo cp /var/app/current/supervisord.service /etc/systemd/system/supervisord.service
    sudo systemctl start supervisord
}

#if key exists and is true

echo "Found worker key!"
echo "Starting worker deploy process...";

if [ -f /etc/init.d/supervisord ];
    then
       echo "Config found. Supervisor already installed"
       updateSupervisor
    else
       echo "No supervisor config found. Installing supervisor..."
       installSupervisor
    fi;

echo "Deployment done!"