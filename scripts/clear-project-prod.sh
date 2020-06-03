#!/bin/bash

## Removes all files and subfolders ##
rm -rf /var/www/bilemo/*

## Find all hidden files and delete it ##
find /var/www/bilemo/ -maxdepth 1 -type f -name ".*" -delete