#!/bin/bash

java -jar $YUICOMPRESSOR -o site.min.js site.js
rsync -av --delete --delete-excluded --exclude "misc/" --exclude "*.swp" --exclude ".git/" . nolan@nolancaudill.com:/home/nolan/src/flickrtrickle.com/
