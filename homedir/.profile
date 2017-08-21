#############################################################
# Generic configuration that applies to all shells
#############################################################

source ~/.shellvars
# Include custom shell variables
source ~/.ss_shellvars
source ~/.shellfn
# Include custom shell functions
source ~/.ss_shellfn
source ~/.shellpaths
source ~/.shellaliases
# Include custom shell aliases
source ~/.ss_shellaliases
source ~/.iterm2_shell_integration.`basename $SHELL`
# Private/Proprietary shell aliases (not to be checked into the public repo) :)
#source ~/Dropbox/Private/Boxes/osx/.shellaliases
