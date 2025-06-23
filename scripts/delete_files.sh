#!/bin/bash

# Script to find and delete files matching a pattern in a target directory.

# Usage: ./delete_files.sh <pattern> <target_directory>

# Check if the correct number of arguments is provided
if [ $# -ne 2 ]; then
    echo "Usage: $0 <pattern> <target_directory>"
    exit 1
fi

# Assign arguments to variables
pattern="$1"
target_directory="$2"

# Check if the target directory exists
if [ ! -d "$target_directory" ]; then
    echo "Error: Target directory '$target_directory' does not exist."
    exit 1
fi

# Loop until no more files are found matching the pattern
while true; do
    if find "$target_directory" -type f -name "*$pattern*" -print -delete; then
        echo "Files matching pattern '$pattern' deleted in this iteration in $target_directory."
        sleep 5
    else
        echo "No more files found matching pattern '$pattern' in '$target_directory'."
        break
    fi
done

echo "Script completed for pattern $pattern in $target_directory"

exit 0
