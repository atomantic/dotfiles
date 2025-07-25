##############################################################################
#Import the shell-agnostic (Bash or Zsh) environment config
##############################################################################
source ~/.profile

##############################################################################
# History Configuration
##############################################################################
HISTSIZE=5000               #How many lines of history to keep in memory
HISTFILE=~/.zsh_history     #Where to save history to disk
SAVEHIST=5000               #Number of history entries to save to disk
HISTDUP=erase               #Erase duplicates in the history file
setopt    appendhistory     #Append history to the history file (no overwriting)
setopt    sharehistory      #Share history across terminals
setopt    incappendhistory  #Immediately append to the history file, not just when a term is killed

##############################################################################
# z-zsh setup
##############################################################################
. ~/.dotfiles/z-zsh/z.sh
function precmd () {
  z --add "$(pwd -P)"
}

eval "$(/opt/homebrew/bin/brew shellenv)"
# Add Visual Studio Code (code)
export PATH="$PATH:/Applications/Visual Studio Code.app/Contents/Resources/app/bin"