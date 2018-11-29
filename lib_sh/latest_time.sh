#!/usr/bin/env zsh
latestProfile=$(ls -lt ~/zsh_profile.* | awk '{print $9}' | head -1)
./sort_timings.zsh $latestProfile | head

