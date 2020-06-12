#!/bin/bash

## Removes all files and subfolders ##
rm -rf /var/www/deployDirectory/*

## Find all hidden files and delete it ##
find /var/www/deployDirectory/ -maxdepth 1 -type f -name ".*" -delete