#!/bin/bash
ver=3.1.7
./dump-db.sh root $ver
mysql -e "drop database poundcake" mysql
mysql -e "create database poundcake" mysql
mysql < db/poundcake-$ver.sql