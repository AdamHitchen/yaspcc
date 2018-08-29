#!/usr/bin/env bash
#{{FOR DEV ONLY}} volume permission issues workaround - requires process configured to run as 'php'

# get the group & user ID of the source directory
TARGET_GID=$(stat -c "%g" /source)
TARGET_UID=$(stat -c "%u" /source)

EXISTS=$(cat /etc/group | grep $TARGET_GID | wc -l)

# Create a new user using UID and group using GID
if ! id -u php; then
    useradd -u $TARGET_UID php
fi

if [ $EXISTS == "0" ]; then
    groupadd -g $TARGET_GID php
    usermod -a -G php php
else

# GID exists, find group name and add
GROUP=$(getent group $TARGET_GID | cut -d: -f1)
usermod -a -G $GROUP php
usermod -a -G $GROUP www-data
fi

sed -i 's/www-data/php/i' /usr/local/etc/php-fpm.d/*

php-fpm -F
