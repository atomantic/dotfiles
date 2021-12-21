tap "homebrew/bundle"
tap "homebrew/cask"
tap "homebrew/cask-drivers"
tap "homebrew/cask-fonts"
tap "homebrew/cask-versions"
tap "homebrew/core"
tap "homebrew/services"
tap "homebrew/dupes"
# http://conqueringthecommandline.com/book/ack_ag
brew "ack"
brew "ag"
# https://github.com/wting/autojump
brew "autojump"
# alternative to `cat`: https://github.com/sharkdp/bat
brew "bat"
# Install GNU core utilities (those that come with macOS are outdated)
# Don’t forget to add `$(brew --prefix coreutils)/libexec/gnubin` to `$PATH`.
brew "coreutils"
brew "dos2unix"
# Install GNU `find`, `locate`, `updatedb`, and `xargs`, `g`-prefixed
brew "findutils"
# brew "fortune"
brew "fzf"
brew "readline" # ensure gawk gets good readline
brew "gawk"
# http://www.lcdf.org/gifsicle/ (because I'm a gif junky)
brew "gifsicle"
brew "gnupg"
# Install GNU `sed`, overwriting the built-in `sed`
# so we can do "sed -i 's/foo/bar/' file" instead of "sed -i '' 's/foo/bar/' file"
brew "gnu-sedbrew", args: ["--with-default-names"]
# upgrade grep so we can get things like inverted match (-v)
brwe "grepbrew", args: ["--with-default-names"]
# better, more recent grep
brew "homebrew/dupes/grep"
# https://github.com/jkbrzt/httpie
brew "httpie"
# jq is a sort of JSON grep
brew "jq"
# Mac App Store CLI: https://github.com/mas-cli/mas
brew "mas"
# Install some other useful utilities like `sponge`
brew "moreutils"
brew "nmap"
# brew "openconnect"
brew "reattach-to-user-namespace"
# better/more recent version of screen
brew "homebrew/dupes/screen"
brew "tmux"
brew "todo-txt"
brew "tree"
brew "ttyrec"
# better, more recent vim
brew "vim", args: ["--with-client-serverbrew", "--with-override-system-vi"]
brew "watch"
# Install wget with IRI support
brew "wget", args: ["--enable-iri"]
# Homebrew Casks
# cask "adium"
# cask "amazon-cloud-drive"
# cask "atom"
# cask "box-sync"
# cask "comicbooklover"
# cask "diffmerge"
cask "docker" # docker for mac
# cask "dropbox"
# cask "evernote"
cask "flux"
cask "gpg-suite"
# cask "ireadfast"
cask "iterm2"
cask "little-snitch"
# cask "macbreakz"
cask "micro-snitch"
# cask "signal"
# cask "macvim"
cask "sizeup"
# cask "sketchup"
cask "slack"
# cask "the-unarchiver"
# cask "torbrowser"
# cask "transmission"
cask "visual-studio-code"
# cask "vlc"
cask "xquartz"
# Mac AppStore Apps
mas "Xcode", id: 497799835
# mas "Amphetamine", id: 937984704
# mas "bear", id: 1091189122
# mas "monosnap", id: 540348655
# mas "pwsafemac", id: 520993579
