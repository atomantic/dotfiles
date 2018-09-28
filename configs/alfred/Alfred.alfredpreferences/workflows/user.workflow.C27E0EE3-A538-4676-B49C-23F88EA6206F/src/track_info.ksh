#!/bin/ksh

osascript <<EOT
try
	tell application "Spotify"
		if the player state is stopped then
			set theResult to "stopped"
			try
				if current track is not missing value then set theResult to "paused"
			end try
		else if the player state is paused then
			set theResult to "paused"
		else
			set theResult to "playing"
		end if
		return name of current track & "▹" & artist of current track & "▹" & album of current track & "▹" & theResult & "▹" & spotify url of current track & "▹" & duration of current track & "▹" & popularity of current track
	end tell
on error error_message
	return
end try
EOT