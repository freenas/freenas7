#!/bin/sh
# Check gmirror RAID:
gmirror list | \
   awk -F: '/^Geom name:/ {
               name=$2
            }
            /^State:/ {
               print name ":" $2
            }' | awk -F": " '$2 !~ /COMPLETE/ { print $1 ":" $2 }'

gstripe list | \
   awk -F: '/^Geom name:/ {
               name=$2
            }
            /^State:/ {
               print name ":" $2
            }' | awk -F": " '$2 !~ /UP/ { print $1 }'
            
gconcat list | \
   awk -F: '/^Geom name:/ {
               name=$2
            }
            /^State:/ {
               print name ":" $2
            }' | awk -F": " '$2 !~ /UP/ { print $1 }'
