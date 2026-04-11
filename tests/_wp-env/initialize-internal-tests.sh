#!/bin/bash

# Print the script name.
echo $(basename "$0")

echo "Installing latest build of bh-wp-simple-calendar"
wp plugin install ../setup/bh-wp-simple-calendar.latest.zip --activate --force
