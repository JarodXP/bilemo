#!/bin/bash

## Removes all files and subfolders ##
rm -rf /var/www/bilemo-test/*

## Find all hidden files and delete it ##
find /var/www/bilemo-test/ -maxdepth 1 -type f -name ".*" -delete