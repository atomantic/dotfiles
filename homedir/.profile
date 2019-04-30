#############################################################
# Generic configuration that applies to all shells
#############################################################

source ~/.shellpaths
source ~/.shellaliases

#source ~/.iterm2_shell_integration.`basename $SHELL`

# Add RVM to PATH for scripting. Make sure this is the last PATH variable change.
export PATH="$PATH:$HOME/.rvm/bin"

export PATH=~/usr/bin/python:$PATH
[[ -s "$HOME/.rvm/scripts/rvm" ]] && source "$HOME/.rvm/scripts/rvm" # Load RVM into a shell session *as a function*
source /Users/willlong/repos/scripts/init.sh
