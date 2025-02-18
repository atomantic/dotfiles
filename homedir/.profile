#############################################################
# Generic configuration that applies to all shells
#############################################################

source ~/.shellvars
source ~/.shellfn
source ~/.shellpaths
source ~/.shellaliases
source ~/.iterm2_shell_integration.`basename $SHELL`
# Private/Proprietary shell aliases (not to be checked into the public repo) :)
#source ~/Dropbox/Private/Boxes/osx/.shellaliases


# Added by LM Studio CLI (lms)
export PATH="$PATH:/Users/marc/.cache/lm-studio/bin"
. "$HOME/.cargo/env"
