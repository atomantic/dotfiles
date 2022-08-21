# Fig pre block. Keep at the top of this file.
[[ -f "$HOME/.fig/shell/profile.pre.bash" ]] && . "$HOME/.fig/shell/profile.pre.bash"
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

# Fig post block. Keep at the bottom of this file.
[[ -f "$HOME/.fig/shell/profile.post.bash" ]] && . "$HOME/.fig/shell/profile.post.bash"
