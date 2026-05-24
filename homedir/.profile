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

# Added by LM Studio CLI (lms)
export PATH="$PATH:/Users/marc/.cache/lm-studio/bin"
# End of LM Studio CLI section

# Added by LM Studio CLI (lms)
export PATH="$PATH:/Users/dreamora/.lmstudio/bin"
# End of LM Studio CLI section

# Atuin
if command -v atuin &>/dev/null; then
	if [ -d "$HOME/.atuin/bin/env" ]; then
		source "$HOME/.atuin/bin/env"
	fi
	eval "$(atuin init zsh)"
else
	echo 'Atuin not installed, skipping configuration'
fi
# End Atuin
