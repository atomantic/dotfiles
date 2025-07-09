#############################################################
# Generic configuration that applies to all shells
#############################################################

source ~/.shellvars
source ~/.shellfn
source ~/.shellpaths
source ~/.shellaliases

if [ -d "$HOME/.cache/lm-studio/bin" ]; then
  export PATH="$PATH:$HOME/.cache/lm-studio/bin"
fi

if [ -d "$HOME/.cargo" ]; then
  source "$HOME/.cargo/env"
fi
