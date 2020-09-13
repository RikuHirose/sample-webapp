#!/usr/bin/env bash
# Author:      Matt Foxx Duncan <matt.duncan13@gmail.com>
#
# Description: For use with AWS elasticbeanstalk and Laravel. As part of a deployment will either skip, install,
#              or update supervisord depending on environmental variables set in EB.

updateSupervisor(){
    cp /var/app/current/supervisord.conf /etc/supervisord.conf
    # sudo service supervisord stop
    # php /var/app/current/artisan queue:restart # If this worker is running in daemon mode (most likely) we need to restart it with the new build
    # echo "Sleeping a few seconds to make sure supervisor shuts down..." # https://github.com/Supervisor/supervisor/issues/48#issuecomment-2684400
    # sleep 5
    # sudo service supervisord start
}

installSupervisor(){
    pip install --install-option="--install-scripts=/usr/bin" supervisor --pre
    # sudo cp /var/app/current/supervisord /etc/init.d/supervisord
    # chmod 777 /etc/init.d/supervisord
    # mkdir -m 766 /var/log/supervisor
    # umask 022
    # touch /var/log/supervisor/supervisord.log
    cp /var/app/current/supervisord.conf /etc/supervisord.conf
    # /etc/init.d/supervisord  start
    # sudo chkconfig supervisord  on
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