#!/bin/bash
#
# initial setup:
# mkdir poundcake; git clone -b addrpool-2 git@github.com:clarkritchie/poundcake.git

# backup the database
function backup {
	if [[ $BACKUP != "" ]]; then 
		# mysqldump --extended-insert=FALSE --routines --opt poundcake > $BACKUP_FILE
		echo "Backing up MySQL database"
		mysqldump --skip-extended-insert --routines --opt -uroot -p$BACKUP poundcake > $BACKUP_FILE
	else
		echo "No MySQL backup"
	fi
}

# get the code from GitHub
function deploy {
	# put the site into maintenance mode
	sudo sh -c "echo 1 > $MAINT_FILE"
	cd $TOWERDB/poundcake
	sudo chown -R ubuntu $TOWERDB
	git pull origin $BRANCH
	sudo chown -R www-data $TOWERDB
	echo "*** REMEMBER TO DISABLE MAINTENANCE MODE ***"
	echo "    sudo sh -c \"echo 0 > $MAINT_FILE\" "
}

function opensite {
	read -p "Disable site maintenance mode?  [Press Y to disable] " yn
    case $yn in
        [Yy]* )
        	sudo sh -c "echo 0 > $MAINT_FILE"
			exit;;
        * )
        	echo "********************************************"
        	echo "*** REMEMBER TO DISABLE MAINTENANCE MODE ***"
			echo "     sudo sh -c \"echo 0 > $MAINT_FILE\" "
        	echo "********************************************"
        	exit;;
    esac
}

function usage {
cat << EOF
usage: $0 options

This script will deploy the Poundcake application from GitHub.

OPTIONS:
   -h      	Show this message
   -g      	Git branch to pull from
   -d      	Destination, use: "-d s" for staging (default) or "-d p" for production
   -b		MySQL root password (to backup current database), it none specified no backup is performed	
EOF
}

# http://rsalveti.wordpress.com/2007/04/03/bash-parsing-arguments-with-getopts/
while getopts "hg:d:b:" OPTION
do
     case $OPTION in
        h)
             usage
             exit 1
             ;;
        g)
             BRANCH=$OPTARG
             ;;
    	b)
             BACKUP=$OPTARG
             ;;
        d)
             DEST=$OPTARG
             ;;
        ?)
             usage
             exit
             ;;
     esac
done

#echo $BRANCH
#echo $DEST
#echo $BACKUP

if [[ -z $BRANCH ]] || [[ -z $DEST ]]
then
     usage
     exit 1
fi

if  [[ $DEST == "p" ]]; then 
	TOWERDB=/opt/www/towerdb.inveneo.org
else
	TOWERDB=/var/www/towerdb-staging.inveneo.org
fi

NOW=$(date +"%m_%d_%Y_at_%H_%M")

MAINT_FILE="$TOWERDB/poundcake/app/maintenance.txt"
BACKUP_DIR="/home/ubuntu/poundcake"
BACKUP_FILE=$BACKUP_DIR"/poundcake-pre-"$BRANCH"-"$NOW".sql"

echo "This will deploy Tower DB using:"
echo ""
echo "Branch: $BRANCH"
echo "Path:  $TOWERDB"
echo "DB backup (if specified):  $BACKUP_FILE"
echo ""

while true; do
    read -p "Do you wish to deploy the site?  [Press Y to continue] " yn
    case $yn in
        [Yy]* )
        	backup
        	deploy
        	opensite
			exit;;
        [Nn]* ) exit;;
        * ) echo "Please answer Y or y to continue deployment.";;
    esac
done

# cleanup old files
find $BACKUP_DIR -type f -mtime +14 -exec rm -f {} \;

exit 0
