#!/bin/bash
# Usage:  ./dump-db.sh root 3.1.6
# will produce a .sql file named poundcake-3.1.6.sql

if [ $# -eq 0 ] ; then
    echo 'Missing args, Usage: '$0' [MySQL root password] [version]'
    exit 0
fi

DIR=db
mkdir -p $DIR;

DB=poundcake

FILE=$DB
if [ -n "$2" ]; then
    FILE=$FILE-$2
fi
FILE=$DIR/$FILE.sql

mysqldump  --opt --no-data --routines  --user root --password=$1 $DB > $FILE;

mysqldump --user root --password=$1 $DB site_states site_state_icons switch_types tower_types tower_mounts tower_members zones power_types antenna_types antenna_types_radio_types build_item_types change_logs connectivity_types contact_types equipment_spaces frequencies monitoring_system_types network_services >> $FILE
# get one reasonable set of site_states
mysqldump --user root --password=$1 $DB site_states --where="project_id=1" >> $FILE
# roles
mysqldump --user root --password=$1 $DB roles >> $FILE

# add administrator into schema; username/password is:  admin/secret
echo "INSERT INTO users(username,password,admin) VALUES ('admin','e9207779e97900bb506e145e2d383a90c0c7c2e6',1);" >> $FILE
# create a default project
echo "INSERT INTO projects(id,name,default_lat,default_lon) VALUES (1,'My Wi-Fi Project',45.52,-122.681944);" >> $FILE
# give user admin permissions

echo "INSERT INTO project_memberships(project_id,user_id,role_id) VALUES (1,1,1);" >> $FILE