#!/bin/ksh

osascript <<EOT
if application "Spotify" is not running then
	return "1"
end if

return 0
EOT