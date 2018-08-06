#!/bin/sh
find app/tmp -type d -exec chmod 775 {} \;
find app/tmp -type f -exec chmod 664 {} \;
chown -R critchie:www-data app/tmp 
