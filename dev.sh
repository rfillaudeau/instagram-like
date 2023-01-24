#!/bin/sh

sudo docker compose cp app:/var/www/app/node_modules/ ./
sudo docker compose cp app:/var/www/app/vendor/ ./
