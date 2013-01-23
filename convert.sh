#!/bin/sh

# You might need to change the path to the actual location of convert
/usr/bin/convert $1 -colorspace Gray -resize 376 $2