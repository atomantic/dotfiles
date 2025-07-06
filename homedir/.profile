#############################################################
# Generic configuration that applies to all shells
#############################################################

source ~/.shellvars
source ~/.shellfn
source ~/.shellpaths
source ~/.shellaliases
# source ~/.iterm2_shell_integration.$(basename $SHELL)

# Private/Proprietary shell aliases (not to be checked into the public repo) :)
#source ~/Dropbox/Private/Boxes/osx/.shellaliases

if [ -d "$HOME/.cache/lm-studio/bin" ]; then
  export PATH="$PATH:$HOME/.cache/lm-studio/bin"
fi

if [ -d "$HOME/.cargo" ]; then
  . "$HOME/.cargo/env"
fi
