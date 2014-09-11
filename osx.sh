#!/usr/bin/env bash

# include my library helpers for colorized echo and require_brew, etc
source ./lib.sh

# Ask for the administrator password upfront
bot "I need you to enter your sudo password so I can install some things:"
sudo -v

# Keep-alive: update existing `sudo` time stamp until `.osx` has finished
while true; do sudo -n true; sleep 60; kill -0 "$$" || exit; done 2>/dev/null &


#####
# install homebrew
#####

running "checking homebrew"
brew_bin=$(which brew) 2>&1 > /dev/null
if [[ $? != 0 ]]; then
	action "installing homebrew"
    ruby -e "$(curl -fsSL https://raw.github.com/Homebrew/homebrew/go/install)"
    if [[ $? != 0 ]]; then
    	error "unable to install homebrew, script $0 abort!"
    	exit -1
	fi
fi
ok

running "checking brew-cask"
output=$(brew tap | grep cask)
if [[ $? != 0 ]]; then
	action "installing brew-cask"
	require_brew caskroom/cask/brew-cask
fi
ok

###############################################################################
#Install command-line tools using Homebrew                                    #
###############################################################################
bot "installing command-line tools via homebrew..."
# Make sure we’re using the latest Homebrew
action "update brew..."
brew update
ok "brew updated..."
# Upgrade any already-installed formulae
action "upgrade brew packages..."
brew upgrade
ok "brews updated..."

action "installing packages..."


# Install GNU core utilities (those that come with OS X are outdated)
# Don’t forget to add `$(brew --prefix coreutils)/libexec/gnubin` to `$PATH`.
require_brew coreutils
# Install some other useful utilities like `sponge`
require_brew moreutils
# Install GNU `find`, `locate`, `updatedb`, and `xargs`, `g`-prefixed
require_brew findutils

# Install Bash 4
# Note: don’t forget to add `/usr/local/bin/bash` to `/etc/shells` before running `chsh`.
#install bash
#install bash-completion

# Install RingoJS and Narwhal
# Note that the order in which these are installed is important; see http://git.io/brew-narwhal-ringo.
#install ringojs
#install narwhal

# Install other useful binaries
require_brew ack
# Beanstalk http://kr.github.io/beanstalkd/
#require_brew beanstalkd
# ln -sfv /usr/local/opt/beanstalk/*.plist ~/Library/LaunchAgents
# launchctl load ~/Library/LaunchAgents/homebrew.mxcl.beanstalk.plist
require_brew boot2docker
# dos2unix converts windows newlines to unix newlines
require_brew dos2unix
require_brew fig
# fortune command--I source this as a better motd :)
require_brew fortune
require_brew gawk
# http://www.lcdf.org/gifsicle/ (because I'm a gif junky)
require_brew gifsicle
# skip those GUI clients, git command-line all the way
require_brew git
# yes, yes, use git-flow, please :)
require_brew git-flow
# why is everyone still not using GPG?
require_brew gnupg
# Install GNU `sed`, overwriting the built-in `sed`
# so we can do "sed -i 's/foo/bar' file" instead of "sed -i '' 's/foo/bar' file"
require_brew gnu-sed --default-names
# better, more recent grep
require_brew homebrew/dupes/grep
require_brew hub
require_brew imagemagick
require_brew imagesnap
# jq is a JSON grep
require_brew jq
# http://maven.apache.org/
require_brew maven
require_brew memcached
require_brew nmap
require_brew node
require_brew redis
# better/more recent version of screen
require_brew homebrew/dupes/screen
require_brew tig
require_brew tree
require_brew ttyrec
# better, more recent vim
require_brew vim --override-system-vi
# Install wget with IRI support
require_brew wget --enable-iri

bot "if you would like to start memcached at login, run this:"
echo "ln -sfv /usr/local/opt/memcached/*.plist ~/Library/LaunchAgents"
bot "if you would like to start memcached now, run this:"
echo "launchctl load ~/Library/LaunchAgents/homebrew.mxcl.memcached.plist"

###############################################################################
# Native Apps (via brew cask)                                                 #
###############################################################################
bot "installing GUI tools via homebrew casks..."
brew tap caskroom/versions > /dev/null 2>&1

# cloud storage
require_cask amazon-cloud-drive
require_cask box-sync
require_cask dropbox
require_cask evernote
require_cask skydrive

# communication
require_cask adium
require_cask slack

# tools
#require_cask comicbooklover
require_cask diffmerge
#require_cask flash-player
require_cask github
require_cask gpgtools
require_cask ireadfast
require_cask iterm2
require_cask lastpass-universal
#require_cask macvim
require_cask sizeup
#require_cask simple-comic
#require_cask sketchup
require_cask sublime-text
require_cask the-unarchiver
#require_cask transmission
require_cask vlc
require_cask xquartz

# development browsers
require_cask breach
require_cask firefox
#require_cask firefox-aurora
require_cask google-chrome
require_cask google-chrome-canary
require_cask torbrowser

# virtal machines
require_cask virtualbox
# chef-dk, berkshelf, etc
require_cask chefdk
# vagrant for running dev environments using docker images
require_cask vagrant # # | grep Caskroom | sed "s/.*'\(.*\)'.*/open \1\/Vagrant.pkg/g" | sh

bot "Alright, cleaning up homebrew cache..."
# Remove outdated versions from the cellar
brew cleanup > /dev/null 2>&1
bot "All clean"

###############################################################################
bot "Configuring General System UI/UX..."
###############################################################################


###############################################################################
# SSD-specific tweaks                                                         #
###############################################################################

running "Disable local Time Machine snapshots"
sudo tmutil disablelocal;ok

running "Disable hibernation (speeds up entering sleep mode)"
sudo pmset -a hibernatemode 0;ok

running "Remove the sleep image file to save disk space"
sudo rm -rf /Private/var/vm/sleepimage;ok
running "Create a zero-byte file instead…"
sudo touch /Private/var/vm/sleepimage;ok
running "…and make sure it can’t be rewritten"
sudo chflags uchg /Private/var/vm/sleepimage;ok

#running "Disable the sudden motion sensor as it’s not useful for SSDs"
# sudo pmset -a sms 0;ok

################################################
# Optional / Experimental                      #
################################################

# running "Set computer name (as done via System Preferences → Sharing)"
# sudo scutil --set ComputerName "antic"
# sudo scutil --set HostName "antic"
# sudo scutil --set LocalHostName "antic"
# sudo defaults write /Library/Preferences/SystemConfiguration/com.apple.smb.server NetBIOSName -string "antic"

# running "Disable smooth scrolling"
# (Uncomment if you’re on an older Mac that messes up the animation)
# defaults write NSGlobalDomain NSScrollAnimationEnabled -bool false;ok

# running "Disable Resume system-wide"
# defaults write NSGlobalDomain NSQuitAlwaysKeepsWindows -bool false;ok
# TODO: might want to enable this again and set specific apps that this works great for
# e.g. defaults write com.microsoft.word NSQuitAlwaysKeepsWindows -bool true

# running "Fix for the ancient UTF-8 bug in QuickLook (http://mths.be/bbo)""
# # Commented out, as this is known to cause problems in various Adobe apps :(
# # See https://github.com/mathiasbynens/dotfiles/issues/237
# echo "0x08000100:0" > ~/.CFUserTextEncoding;ok

# running "Stop iTunes from responding to the keyboard media keys"
# launchctl unload -w /System/Library/LaunchAgents/com.apple.rcd.plist 2> /dev/null;ok

# running "Show icons for hard drives, servers, and removable media on the desktop"
# defaults write com.apple.finder ShowExternalHardDrivesOnDesktop -bool true
# defaults write com.apple.finder ShowHardDrivesOnDesktop -bool true
# defaults write com.apple.finder ShowMountedServersOnDesktop -bool true
# defaults write com.apple.finder ShowRemovableMediaOnDesktop -bool true;ok

# running "Enable the MacBook Air SuperDrive on any Mac"
# sudo nvram boot-args="mbasd=1";ok

# running "Remove Dropbox’s green checkmark icons in Finder"
# file=/Applications/Dropbox.app/Contents/Resources/emblem-dropbox-uptodate.icns
# [ -e "${file}" ] && mv -f "${file}" "${file}.bak";ok

# running "Wipe all (default) app icons from the Dock"
# # This is only really useful when setting up a new Mac, or if you don’t use
# # the Dock to launch apps.
# defaults write com.apple.dock persistent-apps -array "";ok

#running "Enable the 2D Dock"
#defaults write com.apple.dock no-glass -bool true;ok

#running "Disable the Launchpad gesture (pinch with thumb and three fingers)"
#defaults write com.apple.dock showLaunchpadGestureEnabled -int 0;ok

#running "Add a spacer to the left side of the Dock (where the applications are)"
#defaults write com.apple.dock persistent-apps -array-add '{tile-data={}; tile-type="spacer-tile";}';ok
#running "Add a spacer to the right side of the Dock (where the Trash is)"
#defaults write com.apple.dock persistent-others -array-add '{tile-data={}; tile-type="spacer-tile";}';ok

running "Set a custom wallpaper image."
# `DefaultDesktop.jpg` is already a symlink, and
# all wallpapers are in `/Library/Desktop Pictures/`. The default is `Wave.jpg`.
rm -rf ~/Library/Application Support/Dock/desktoppicture.db
sudo rm -rf /System/Library/CoreServices/DefaultDesktop.jpg
sudo ln -s ~/.dotfiles/img/wallpaper.jpg /System/Library/CoreServices/DefaultDesktop.jpg;ok


################################################
bot "Standard System Changes"
################################################
running "always boot in verbose mode (not OSX GUI mode)"
sudo nvram boot-args="-v";ok

running "allow 'locate' command"
sudo launchctl load -w /System/Library/LaunchDaemons/com.apple.locate.plist > /dev/null 2>&1;ok

running "Set standby delay to 24 hours (default is 1 hour)"
sudo pmset -a standbydelay 86400;ok

running "Disable the sound effects on boot"
sudo nvram SystemAudioVolume=" ";ok

running "Menu bar: disable transparency"
defaults write NSGlobalDomain AppleEnableMenuBarTransparency -bool false;ok

running "Menu bar: hide the Time Machine, Volume, User, and Bluetooth icons"
for domain in ~/Library/Preferences/ByHost/com.apple.systemuiserver.*; do
	defaults write "${domain}" dontAutoLoad -array \
		"/System/Library/CoreServices/Menu Extras/TimeMachine.menu" \
		"/System/Library/CoreServices/Menu Extras/Volume.menu" \
		"/System/Library/CoreServices/Menu Extras/User.menu"
done;
defaults write com.apple.systemuiserver menuExtras -array \
	"/System/Library/CoreServices/Menu Extras/Bluetooth.menu" \
	"/System/Library/CoreServices/Menu Extras/AirPort.menu" \
	"/System/Library/CoreServices/Menu Extras/Battery.menu" \
	"/System/Library/CoreServices/Menu Extras/Clock.menu"
ok

running "Set highlight color to green"
defaults write NSGlobalDomain AppleHighlightColor -string "0.764700 0.976500 0.568600";ok

running "Set sidebar icon size to medium"
defaults write NSGlobalDomain NSTableViewDefaultSizeMode -int 2;ok

running "Always show scrollbars"
defaults write NSGlobalDomain AppleShowScrollBars -string "Always";ok
# Possible values: `WhenScrolling`, `Automatic` and `Always`

running "Increase window resize speed for Cocoa applications"
defaults write NSGlobalDomain NSWindowResizeTime -float 0.001;ok

running "Expand save panel by default"
defaults write NSGlobalDomain NSNavPanelExpandedStateForSaveMode -bool true
defaults write NSGlobalDomain NSNavPanelExpandedStateForSaveMode2 -bool true;ok

running "Expand print panel by default"
defaults write NSGlobalDomain PMPrintingExpandedStateForPrint -bool true
defaults write NSGlobalDomain PMPrintingExpandedStateForPrint2 -bool true;ok

running "Save to disk (not to iCloud) by default"
defaults write NSGlobalDomain NSDocumentSaveNewDocumentsToCloud -bool false;ok

running "Automatically quit printer app once the print jobs complete"
defaults write com.apple.print.PrintingPrefs "Quit When Finished" -bool true;ok

running "Disable the “Are you sure you want to open this application?” dialog"
defaults write com.apple.LaunchServices LSQuarantine -bool false;ok

running "Remove duplicates in the “Open With” menu (also see 'lscleanup' alias)"
/System/Library/Frameworks/CoreServices.framework/Frameworks/LaunchServices.framework/Support/lsregister -kill -r -domain local -domain system -domain user;ok

running "Display ASCII control characters using caret notation in standard text views"
# Try e.g. `cd /tmp; unidecode "\x{0000}" > cc.txt; open -e cc.txt`
defaults write NSGlobalDomain NSTextShowsControlCharacters -bool true;ok

running "Disable automatic termination of inactive apps"
defaults write NSGlobalDomain NSDisableAutomaticTermination -bool true;ok

running "Disable the crash reporter"
defaults write com.apple.CrashReporter DialogType -string "none";ok

running "Set Help Viewer windows to non-floating mode"
defaults write com.apple.helpviewer DevMode -bool true;ok

running "Reveal IP, hostname, OS, etc. when clicking clock in login window"
sudo defaults write /Library/Preferences/com.apple.loginwindow AdminHostInfo HostName;ok

running "Restart automatically if the computer freezes"
sudo systemsetup -setrestartfreeze on;ok

running "Never go into computer sleep mode"
sudo systemsetup -setcomputersleep Off > /dev/null;ok

running "Check for software updates daily, not just once per week"
defaults write com.apple.SoftwareUpdate ScheduleFrequency -int 1;ok

running "Disable Notification Center and remove the menu bar icon"
launchctl unload -w /System/Library/LaunchAgents/com.apple.notificationcenterui.plist > /dev/null 2>&1;ok

running "Disable smart quotes as they’re annoying when typing code"
defaults write NSGlobalDomain NSAutomaticQuoteSubstitutionEnabled -bool false;ok

running "Disable smart dashes as they’re annoying when typing code"
defaults write NSGlobalDomain NSAutomaticDashSubstitutionEnabled -bool false;ok


###############################################################################
bot "Trackpad, mouse, keyboard, Bluetooth accessories, and input"
###############################################################################

running "Trackpad: enable tap to click for this user and for the login screen"
defaults write com.apple.driver.AppleBluetoothMultitouch.trackpad Clicking -bool true
defaults -currentHost write NSGlobalDomain com.apple.mouse.tapBehavior -int 1
defaults write NSGlobalDomain com.apple.mouse.tapBehavior -int 1;ok

running "Trackpad: map bottom right corner to right-click"
defaults write com.apple.driver.AppleBluetoothMultitouch.trackpad TrackpadCornerSecondaryClick -int 2
defaults write com.apple.driver.AppleBluetoothMultitouch.trackpad TrackpadRightClick -bool true
defaults -currentHost write NSGlobalDomain com.apple.trackpad.trackpadCornerClickBehavior -int 1
defaults -currentHost write NSGlobalDomain com.apple.trackpad.enableSecondaryClick -bool true;ok

running "Disable “natural” (Lion-style) scrolling"
defaults write NSGlobalDomain com.apple.swipescrolldirection -bool false;ok

running "Increase sound quality for Bluetooth headphones/headsets"
defaults write com.apple.BluetoothAudioAgent "Apple Bitpool Min (editable)" -int 40;ok

running "Enable full keyboard access for all controls (e.g. enable Tab in modal dialogs)"
defaults write NSGlobalDomain AppleKeyboardUIMode -int 3;ok

running "Use scroll gesture with the Ctrl (^) modifier key to zoom"
defaults write com.apple.universalaccess closeViewScrollWheelToggle -bool true
defaults write com.apple.universalaccess HIDScrollZoomModifierMask -int 262144;ok
running "Follow the keyboard focus while zoomed in"
defaults write com.apple.universalaccess closeViewZoomFollowsFocus -bool true;ok

running "Disable press-and-hold for keys in favor of key repeat"
defaults write NSGlobalDomain ApplePressAndHoldEnabled -bool false;ok

running "Set a blazingly fast keyboard repeat rate"
defaults write NSGlobalDomain KeyRepeat -int 0;

running "Set language and text formats (english/US)"
defaults write NSGlobalDomain AppleLanguages -array "en"
defaults write NSGlobalDomain AppleLocale -string "en_US@currency=USD"
defaults write NSGlobalDomain AppleMeasurementUnits -string "Centimeters"
defaults write NSGlobalDomain AppleMetricUnits -bool true;ok

running "Disable auto-correct"
defaults write NSGlobalDomain NSAutomaticSpellingCorrectionEnabled -bool false;ok

###############################################################################
bot "Configuring the Screen!"
###############################################################################

running "Require password immediately after sleep or screen saver begins"
defaults write com.apple.screensaver askForPassword -int 1
defaults write com.apple.screensaver askForPasswordDelay -int 0;ok

running "Save screenshots to the desktop"
defaults write com.apple.screencapture location -string "${HOME}/Desktop";ok

running "Save screenshots in PNG format (other options: BMP, GIF, JPG, PDF, TIFF)"
defaults write com.apple.screencapture type -string "png";ok

running "Disable shadow in screenshots"
defaults write com.apple.screencapture disable-shadow -bool true;ok

running "Enable subpixel font rendering on non-Apple LCDs"
defaults write NSGlobalDomain AppleFontSmoothing -int 2;ok

running "Enable HiDPI display modes (requires restart)"
sudo defaults write /Library/Preferences/com.apple.windowserver DisplayResolutionEnabled -bool true;ok

###############################################################################
bot "Finder Configs!"
###############################################################################

running "Allow quitting via ⌘ + Q; doing so will also hide desktop icons"
defaults write com.apple.finder QuitMenuItem -bool true;ok

running "Disable window animations and Get Info animations"
defaults write com.apple.finder DisableAllAnimations -bool true;ok

running "Set Desktop as the default location for new Finder windows"
# For other paths, use `PfLo` and `file:///full/path/here/`
defaults write com.apple.finder NewWindowTarget -string "PfDe"
defaults write com.apple.finder NewWindowTargetPath -string "file://${HOME}/Desktop/";ok

running "Show hidden files by default"
defaults write com.apple.finder AppleShowAllFiles -bool true;ok

running "Show all filename extensions"
defaults write NSGlobalDomain AppleShowAllExtensions -bool true;ok

running "Show status bar"
defaults write com.apple.finder ShowStatusBar -bool true;ok

running "Show path bar"
defaults write com.apple.finder ShowPathbar -bool true;ok

running "Allow text selection in Quick Look"
defaults write com.apple.finder QLEnableTextSelection -bool true;ok

running "Display full POSIX path as Finder window title"
defaults write com.apple.finder _FXShowPosixPathInTitle -bool true;ok

running "When performing a search, search the current folder by default"
defaults write com.apple.finder FXDefaultSearchScope -string "SCcf";ok

running "Disable the warning when changing a file extension"
defaults write com.apple.finder FXEnableExtensionChangeWarning -bool false;ok

running "Enable spring loading for directories"
defaults write NSGlobalDomain com.apple.springing.enabled -bool true;ok

running "Remove the spring loading delay for directories"
defaults write NSGlobalDomain com.apple.springing.delay -float 0;ok

running "Avoid creating .DS_Store files on network volumes"
defaults write com.apple.desktopservices DSDontWriteNetworkStores -bool true;ok

running "Disable disk image verification"
defaults write com.apple.frameworks.diskimages skip-verify -bool true
defaults write com.apple.frameworks.diskimages skip-verify-locked -bool true
defaults write com.apple.frameworks.diskimages skip-verify-remote -bool true;ok

running "Automatically open a new Finder window when a volume is mounted"
defaults write com.apple.frameworks.diskimages auto-open-ro-root -bool true
defaults write com.apple.frameworks.diskimages auto-open-rw-root -bool true
defaults write com.apple.finder OpenWindowForNewRemovableDisk -bool true;ok

running "Use list view in all Finder windows by default"
# Four-letter codes for the other view modes: `icnv`, `clmv`, `Flwv`
defaults write com.apple.finder FXPreferredViewStyle -string "Nlsv";ok

running "Disable the warning before emptying the Trash"
defaults write com.apple.finder WarnOnEmptyTrash -bool false;ok

running "Empty Trash securely by default"
defaults write com.apple.finder EmptyTrashSecurely -bool true;ok

running "Enable AirDrop over Ethernet and on unsupported Macs running Lion"
defaults write com.apple.NetworkBrowser BrowseAllInterfaces -bool true;ok

running "Show the ~/Library folder"
chflags nohidden ~/Library;ok


running "Expand the following File Info panes: “General”, “Open with”, and “Sharing & Permissions”"
defaults write com.apple.finder FXInfoPanesExpanded -dict \
	General -bool true \
	OpenWith -bool true \
	Privileges -bool true;ok

###############################################################################
bot "Dock & Dashboard!"
###############################################################################

running "Enable highlight hover effect for the grid view of a stack (Dock)"
defaults write com.apple.dock mouse-over-hilite-stack -bool true;ok

running "Set the icon size of Dock items to 36 pixels"
defaults write com.apple.dock tilesize -int 36;ok

running "Change minimize/maximize window effect to scale"
defaults write com.apple.dock mineffect -string "scale";ok

running "Minimize windows into their application’s icon"
defaults write com.apple.dock minimize-to-application -bool true;ok

running "Enable spring loading for all Dock items"
defaults write com.apple.dock enable-spring-load-actions-on-all-items -bool true;ok

running "Show indicator lights for open applications in the Dock"
defaults write com.apple.dock show-process-indicators -bool true;ok

running "Don’t animate opening applications from the Dock"
defaults write com.apple.dock launchanim -bool false;ok

running "Speed up Mission Control animations"
defaults write com.apple.dock expose-animation-duration -float 0.1;ok

running "Don’t group windows by application in Mission Control"
# (i.e. use the old Exposé behavior instead)
defaults write com.apple.dock expose-group-by-app -bool false;ok

running "Disable Dashboard"
defaults write com.apple.dashboard mcx-disabled -bool true;ok

running "Don’t show Dashboard as a Space"
defaults write com.apple.dock dashboard-in-overlay -bool true;ok

running "Don’t automatically rearrange Spaces based on most recent use"
defaults write com.apple.dock mru-spaces -bool false;ok

running "Remove the auto-hiding Dock delay"
defaults write com.apple.dock autohide-delay -float 0;ok
running "Remove the animation when hiding/showing the Dock"
defaults write com.apple.dock autohide-time-modifier -float 0;ok

running "Automatically hide and show the Dock"
defaults write com.apple.dock autohide -bool true;ok

running "Make Dock icons of hidden applications translucent"
defaults write com.apple.dock showhidden -bool true;ok

running "Make Dock more transparent"
defaults write com.apple.dock hide-mirror -bool true;ok

running "Reset Launchpad, but keep the desktop wallpaper intact"
find "${HOME}/Library/Application Support/Dock" -name "*-*.db" -maxdepth 1 -delete;ok

running "Add iOS Simulator to Launchpad"
sudo ln -sf "/Applications/Xcode.app/Contents/Developer/Platforms/iPhoneSimulator.platform/Developer/Applications/iPhone Simulator.app" "/Applications/iOS Simulator.app";ok


bot "Configuring Hot Corners!"
# Possible values:
#  0: no-op
#  2: Mission Control
#  3: Show application windows
#  4: Desktop
#  5: Start screen saver
#  6: Disable screen saver
#  7: Dashboard
# 10: Put display to sleep
# 11: Launchpad
# 12: Notification Center

running "Top left screen corner → Mission Control"
defaults write com.apple.dock wvous-tl-corner -int 2
defaults write com.apple.dock wvous-tl-modifier -int 0;ok
running "Top right screen corner → Desktop"
defaults write com.apple.dock wvous-tr-corner -int 4
defaults write com.apple.dock wvous-tr-modifier -int 0;ok
running "Bottom right screen corner → Start screen saver"
defaults write com.apple.dock wvous-br-corner -int 5
defaults write com.apple.dock wvous-br-modifier -int 0;ok

###############################################################################
bot "Configuring Safari & WebKit"
###############################################################################

running "Set Safari’s home page to ‘about:blank’ for faster loading"
defaults write com.apple.Safari HomePage -string "about:blank";ok

running "Prevent Safari from opening ‘safe’ files automatically after downloading"
defaults write com.apple.Safari AutoOpenSafeDownloads -bool false;ok

running "Allow hitting the Backspace key to go to the previous page in history"
defaults write com.apple.Safari com.apple.Safari.ContentPageGroupIdentifier.WebKit2BackspaceKeyNavigationEnabled -bool true;ok

running "Hide Safari’s bookmarks bar by default"
defaults write com.apple.Safari ShowFavoritesBar -bool false;ok

running "Hide Safari’s sidebar in Top Sites"
defaults write com.apple.Safari ShowSidebarInTopSites -bool false;ok

running "Disable Safari’s thumbnail cache for History and Top Sites"
defaults write com.apple.Safari DebugSnapshotsUpdatePolicy -int 2;ok

running "Enable Safari’s debug menu"
defaults write com.apple.Safari IncludeInternalDebugMenu -bool true;ok

running "Make Safari’s search banners default to Contains instead of Starts With"
defaults write com.apple.Safari FindOnPageMatchesWordStartsOnly -bool false;ok

running "Remove useless icons from Safari’s bookmarks bar"
defaults write com.apple.Safari ProxiesInBookmarksBar "()";ok

running "Enable the Develop menu and the Web Inspector in Safari"
defaults write com.apple.Safari IncludeDevelopMenu -bool true
defaults write com.apple.Safari WebKitDeveloperExtrasEnabledPreferenceKey -bool true
defaults write com.apple.Safari com.apple.Safari.ContentPageGroupIdentifier.WebKit2DeveloperExtrasEnabled -bool true;ok

running "Add a context menu item for showing the Web Inspector in web views"
defaults write NSGlobalDomain WebKitDeveloperExtras -bool true;ok

###############################################################################
bot "Configuring Mail!"
###############################################################################


running "Disable send and reply animations in Mail.app"
defaults write com.apple.mail DisableReplyAnimations -bool true
defaults write com.apple.mail DisableSendAnimations -bool true;ok

running "Copy email addresses as 'foo@example.com' instead of 'Foo Bar <foo@example.com>' in Mail.app"
defaults write com.apple.mail AddressesIncludeNameOnPasteboard -bool false;ok

running "Add the keyboard shortcut ⌘ + Enter to send an email in Mail.app"
defaults write com.apple.mail NSUserKeyEquivalents -dict-add "Send" -string "@\\U21a9";ok

running "Display emails in threaded mode, sorted by date (oldest at the top)"
defaults write com.apple.mail DraftsViewerAttributes -dict-add "DisplayInThreadedMode" -string "yes"
defaults write com.apple.mail DraftsViewerAttributes -dict-add "SortedDescending" -string "yes"
defaults write com.apple.mail DraftsViewerAttributes -dict-add "SortOrder" -string "received-date";ok

running "Disable inline attachments (just show the icons)"
defaults write com.apple.mail DisableInlineAttachmentViewing -bool true;ok

running "Disable automatic spell checking"
defaults write com.apple.mail SpellCheckingBehavior -string "NoSpellCheckingEnabled";ok

###############################################################################
bot "Spotlight!"
###############################################################################

# running "Hide Spotlight tray-icon (and subsequent helper)"
# sudo chmod 600 /System/Library/CoreServices/Search.bundle/Contents/MacOS/Search;ok

running "Disable Spotlight indexing for any volume that gets mounted and has not yet been indexed"
# Use `sudo mdutil -i off "/Volumes/foo"` to stop indexing any volume.
sudo defaults write /.Spotlight-V100/VolumeConfiguration Exclusions -array "/Volumes";ok
running "Change indexing order and disable some file types from being indexed"
defaults write com.apple.spotlight orderedItems -array \
	'{"enabled" = 1;"name" = "APPLICATIONS";}' \
	'{"enabled" = 1;"name" = "SYSTEM_PREFS";}' \
	'{"enabled" = 1;"name" = "DIRECTORIES";}' \
	'{"enabled" = 1;"name" = "PDF";}' \
	'{"enabled" = 1;"name" = "FONTS";}' \
	'{"enabled" = 0;"name" = "DOCUMENTS";}' \
	'{"enabled" = 0;"name" = "MESSAGES";}' \
	'{"enabled" = 0;"name" = "CONTACT";}' \
	'{"enabled" = 0;"name" = "EVENT_TODO";}' \
	'{"enabled" = 0;"name" = "IMAGES";}' \
	'{"enabled" = 0;"name" = "BOOKMARKS";}' \
	'{"enabled" = 0;"name" = "MUSIC";}' \
	'{"enabled" = 0;"name" = "MOVIES";}' \
	'{"enabled" = 0;"name" = "PRESENTATIONS";}' \
	'{"enabled" = 0;"name" = "SPREADSHEETS";}' \
	'{"enabled" = 0;"name" = "SOURCE";}';ok
running "Load new settings before rebuilding the index"
killall mds > /dev/null 2>&1;ok
running "Make sure indexing is enabled for the main volume"
sudo mdutil -i on / > /dev/null;ok
#running "Rebuild the index from scratch"
#sudo mdutil -E / > /dev/null;ok

###############################################################################
bot "Terminal & iTerm2!"
###############################################################################

running "Only use UTF-8 in Terminal.app"
defaults write com.apple.terminal StringEncodings -array 4;ok

running "Use a modified version of the Solarized Dark theme by default in Terminal.app"
TERM_PROFILE='Solarized Dark xterm-256color';
CURRENT_PROFILE="$(defaults read com.apple.terminal 'Default Window Settings')";
if [ "${CURRENT_PROFILE}" != "${TERM_PROFILE}" ]; then
	open "./configs/${TERM_PROFILE}.terminal";
	sleep 1; # Wait a bit to make sure the theme is loaded
	defaults write com.apple.terminal 'Default Window Settings' -string "${TERM_PROFILE}";
	defaults write com.apple.terminal 'Startup Window Settings' -string "${TERM_PROFILE}";
fi;

#running "Enable “focus follows mouse” for Terminal.app and all X11 apps"
# i.e. hover over a window and start typing in it without clicking first
#defaults write com.apple.terminal FocusFollowsMouse -bool true
#defaults write org.x.X11 wm_ffm -bool true;ok

running "Installing the Solarized Dark theme for iTerm (opening file)"
open "./configs/Solarized Dark.itermcolors";ok

running "Don’t display the annoying prompt when quitting iTerm"
defaults write com.googlecode.iterm2 PromptOnQuit -bool false;ok
running "hide tab title bars"
defaults write com.googlecode.iterm2 HideTab -bool true;ok
running "set system-wide hotkey to show/hide iterm with ^\`"
defaults write com.googlecode.iterm2 Hotkey -bool true;
defaults write com.googlecode.iterm2 HotkeyChar -int 96;
defaults write com.googlecode.iterm2 HotkeyCode -int 50;
defaults write com.googlecode.iterm2 HotkeyModifiers -int 262401;
ok

# running "Make iTerm2 load new tabs in the same directory"
# defaults export com.googlecode.iterm2 /tmp/plist
# /usr/libexec/PlistBuddy -c "set \"New Bookmarks\":0:\"Custom Directory\" Recycle" /tmp/plist
# defaults import com.googlecode.iterm2 /tmp/plist;ok


###############################################################################
bot "Time Machine!"
###############################################################################

running "Prevent Time Machine from prompting to use new hard drives as backup volume"
defaults write com.apple.TimeMachine DoNotOfferNewDisksForBackup -bool true;ok

running "Disable local Time Machine backups"
hash tmutil &> /dev/null && sudo tmutil disablelocal;ok

###############################################################################
bot "Activity Monitor!"
###############################################################################

running "Show the main window when launching Activity Monitor"
defaults write com.apple.ActivityMonitor OpenMainWindow -bool true;ok

running "Visualize CPU usage in the Activity Monitor Dock icon"
defaults write com.apple.ActivityMonitor IconType -int 5;ok

running "Show all processes in Activity Monitor"
defaults write com.apple.ActivityMonitor ShowCategory -int 0;ok

running "Sort Activity Monitor results by CPU usage"
defaults write com.apple.ActivityMonitor SortColumn -string "CPUUsage"
defaults write com.apple.ActivityMonitor SortDirection -int 0;ok

###############################################################################
bot "Address Book, Dashboard, iCal, TextEdit, and Disk Utility!"
###############################################################################

running "Enable the debug menu in Address Book"
defaults write com.apple.addressbook ABShowDebugMenu -bool true;ok

running "Enable Dashboard dev mode (allows keeping widgets on the desktop)"
defaults write com.apple.dashboard devmode -bool true;ok

running "Use plain text mode for new TextEdit documents"
defaults write com.apple.TextEdit RichText -int 0;ok
running "Open and save files as UTF-8 in TextEdit"
defaults write com.apple.TextEdit PlainTextEncoding -int 4
defaults write com.apple.TextEdit PlainTextEncodingForWrite -int 4;ok

running "Enable the debug menu in Disk Utility"
defaults write com.apple.DiskUtility DUDebugMenuEnabled -bool true
defaults write com.apple.DiskUtility advanced-image-options -bool true;ok

###############################################################################
bot "Mac App Store!"
###############################################################################

running "Enable the WebKit Developer Tools in the Mac App Store"
defaults write com.apple.appstore WebKitDeveloperExtras -bool true;ok

running "Enable Debug Menu in the Mac App Store"
defaults write com.apple.appstore ShowDebugMenu -bool true;ok

###############################################################################
bot "Messages!"
###############################################################################

running "Disable automatic emoji substitution (i.e. use plain text smileys)"
defaults write com.apple.messageshelper.MessageController SOInputLineSettings -dict-add "automaticEmojiSubstitutionEnablediMessage" -bool false;ok

running "Disable smart quotes as it’s annoying for messages that contain code"
defaults write com.apple.messageshelper.MessageController SOInputLineSettings -dict-add "automaticQuoteSubstitutionEnabled" -bool false;ok

running "Disable continuous spell checking"
defaults write com.apple.messageshelper.MessageController SOInputLineSettings -dict-add "continuousSpellCheckingEnabled" -bool false;ok

###############################################################################
bot "Google Chrome & Google Chrome Canary"
###############################################################################

running "Allow installing user scripts via GitHub Gist or Userscripts.org"
defaults write com.google.Chrome ExtensionInstallSources -array "https://gist.githubusercontent.com/" "http://userscripts.org/*"
defaults write com.google.Chrome.canary ExtensionInstallSources -array "https://gist.githubusercontent.com/" "http://userscripts.org/*";ok

###############################################################################
bot "SizeUp.app!"
###############################################################################

running "Start SizeUp at login"
defaults write com.irradiatedsoftware.SizeUp StartAtLogin -bool true;ok

running "Don’t show the preferences window on next start"
defaults write com.irradiatedsoftware.SizeUp ShowPrefsOnNextStart -bool false;ok

###############################################################################
bot "Sublime Text!"
###############################################################################

running "Install Sublime Text settings"
cp -r configs/Preferences.sublime-settings ~/Library/Application\ Support/Sublime\ Text*/Packages/User/Preferences.sublime-settings 2> /dev/null;ok

###############################################################################
bot "NPM Globals..."
###############################################################################

require_npm antic
require_npm bower
require_npm forever
require_npm grunt
require_npm gulp
require_npm jshint
# http://devo.ps/blog/goodbye-node-forever-hello-pm2/
require_npm pm2
require_npm prettyjson
require_npm repl-client
require_npm supervisor
require_npm yo

###############################################################################
bot "Ruby Gems..."
###############################################################################
require_gem git-up


###############################################################################
# Kill affected applications                                                  #
###############################################################################
bot "OK. Note that some of these changes require a logout/restart to take effect. Killing affected applications (so they can reboot)...."
for app in "Activity Monitor" "Address Book" "Calendar" "Contacts" "cfprefsd" \
	"Dock" "Finder" "Mail" "Messages" "Safari" "SizeUp" "SystemUIServer" \
	"iCal" "Terminal"; do
	killall "${app}" > /dev/null 2>&1
done