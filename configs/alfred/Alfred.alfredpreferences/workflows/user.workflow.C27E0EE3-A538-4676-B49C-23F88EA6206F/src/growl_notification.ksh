#!/bin/ksh

TITLE=""
SUBTITLE=""

while getopts ':t:s:' arguments
	do
	  case ${arguments} in
		t)
			TITLE="${OPTARG}"
			;;
		s)
			SUBTITLE="${OPTARG}"
			;;
	   \?)
			print "ERROR: ${OPTARG} is not a valid option"
			print "Usage: $0 -t <title> -s <subtitle>"
			exit 1;;
	  esac
	done


osascript <<EOT
tell application "System Events"
	set isRunning to (count of (every process whose bundle identifier is "com.Growl.GrowlHelperApp")) > 0
end tell

if isRunning then
	tell application id "com.Growl.GrowlHelperApp"
		-- Make a list of all the notification types
		-- that this script will ever send:
		set the allNotificationsList to ¬
			{"Spotify Mini Player Notification"}

		-- Make a list of the notifications
		-- that will be enabled by default.
		-- Those not enabled by default can be enabled later
		-- in the 'Applications' tab of the growl prefpane.
		set the enabledNotificationsList to ¬
			{"Spotify Mini Player Notification"}

		-- Register our script with growl.
		-- You can optionally (as here) set a default icon
		-- for this script's notifications.
		register as application ¬
			"Spotify Mini Player" all notifications allNotificationsList ¬
			default notifications enabledNotificationsList ¬
			icon of application "Spotify Mini Player"

		--       Send a Notification...
		notify with name ¬
			"Spotify Mini Player Notification" title ¬
			"${TITLE}" description ¬
			"${SUBTITLE}" application name "Spotify Mini Player"
	end tell
end if
EOT