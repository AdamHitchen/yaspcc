#!/usr/bin/env bash
#{{FOR DEV ONLY}} volume permission issues workaround

# get the group & user ID of the source directory
TARGET_GID=$(stat -c "%g" /yaspcc)
TARGET_UID=$(stat -c "%u" /yaspcc)

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
mkdir /home/php
chown -R php:php /home/php
php-fpm -F
