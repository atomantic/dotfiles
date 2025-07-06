# rosetta terminal setup
if [ $(arch) = "i386" ]; then
  # echo "Initialize i386 based setup"
  alias brew86="/usr/local/bin/brew"
  alias pyenv86="arch -x86_64 pyenv"
  eval "$(/usr/local/bin/brew shellenv)"
 	export PATH="/usr/local/opt/ruby/bin:$PATH"
else
  # echo "Initialize ARM based setup"
  # Fig pre block. Keep at the top of this file.
  eval "$(/opt/homebrew/bin/brew shellenv)"
	export PATH="/opt/homebrew/opt/ruby/bin:$PATH"
fi


# Add jetbrains command line
export PATH="$HOME/Library/Application Support/JetBrains/Toolbox/scripts:$PATH"
# export PATH=/opt/homebrew/bin:/usr/local/bin:$PATH


# AI Dev
if [ -f "$HOME/.private_vars.inc" ]; then
  # echo "Initialize local private variables"
  source "$HOME/.private_vars.inc"
fi

# General
export PATH="/opt/homebrew/opt/openjdk/bin:$PATH"

if [ -d "$HOME/Applications/Android Studio.app" ]; then
  # echo 'Configure Android SDK based on Android Studio'
	export JAVA_HOME="$HOME/Applications/Android Studio.app/Contents/jbr/Contents/Home"
	#export ANDROID_HOME=$HOME/Library/Android/sdk
  export ANDROID_HOME="$ANDRIOD_TOOLING/android-sdk"
	export PATH="$ANDROID_HOME/tools:$ANDROID_HOME/tools/bin:$ANDROID_HOME/platform-tools:$ANDROID_HOME/build-tools/latest:$ANDROID_HOME/cmdline-tools/latest/bin:$PATH"
fi

if [ $(arch) = "i386" ]; then
	export PATH="/usr/local/opt/openjdk@/bin:$PATH"
else
	export PATH="/opt/homebrew/opt/openjdk@/bin:$PATH"
fi

# Path to your oh-my-zsh configuration.
export ZSH=$HOME/.dotfiles/oh-my-zsh
# Set the font mode for powerlevel10k to use JetBrains Mono Nerd Font
POWERLEVEL9K_MODE='nerdfont-complete'
ZSH_THEME="powerlevel10k/powerlevel10k"

# Set to this to use case-sensitive completion
export CASE_SENSITIVE="true"

# disable weekly auto-update checks
# export DISABLE_AUTO_UPDATE="true"

# disable colors in ls
# export DISABLE_LS_COLORS="true"

# disable autosetting terminal title.
export DISABLE_AUTO_TITLE="true"

# Which plugins would you like to load? (plugins can be found in ~/.dotfiles/oh-my-zsh/plugins/*)
# Example format: plugins=(rails git textmate ruby lighthouse)
plugins=(1password autoenv autojump brew colorize compleat cp dirpersist docker docker-compose fzf git git-auto-fetch git-commit gitfast git-hubflow github gulp k9s kubectl kubectx nvm poetry ssh tailscale tmux)

source $ZSH/oh-my-zsh.sh


# if [[ -f /opt/homebrew/opt/nvm/nvm.sh ]]; then
#   source /opt/homebrew/opt/nvm/nvm.sh --no-use
# fi

export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" --no-use # This loads nvm
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion" # This loads nvm bash_completion

autoload -U add-zsh-hook
 export NVM_DIR="$HOME/.nvm"
  [ -s "/usr/local/opt/nvm/nvm.sh" ] && \. "/usr/local/opt/nvm/nvm.sh"  # This loads nvm
  [ -s "/usr/local/opt/nvm/etc/bash_completion.d/nvm" ] && \. "/usr/local/opt/nvm/etc/bash_completion.d/nvm"  # This loads nvm bash_completion
load-nvmrc() {
  if [[ -f .nvmrc && -r .nvmrc ]]; then
    nvm use &> /dev/null # the &> /dev/null ensures that there are no 'now using node version' type messages
  else
    nvm use stable &> /dev/null # the &> /dev/null ensures that there are no 'now using node version' type messages
  fi
}
add-zsh-hook chpwd load-nvmrc
load-nvmrc

# Customize to your needs...
unsetopt correct

# run fortune on new terminal :)
# fortune

# To customize prompt, run `p10k configure` or edit ~/.p10k.zsh.
[[ ! -f ~/.p10k.zsh ]] || source ~/.p10k.zsh

# Enable Powerlevel10k instant prompt. Should stay close to the top of ~/.zshrc.
# Initialization code that may require console input (password prompts, [y/n]
# confirmations, etc.) must go above this block; everything else may go below.
if [[ -r "${XDG_CACHE_HOME:-$HOME/.cache}/p10k-instant-prompt-${(%):-%n}.zsh" ]]; then
  source "${XDG_CACHE_HOME:-$HOME/.cache}/p10k-instant-prompt-${(%):-%n}.zsh"
fi

# Ruby Paths
export GEM_HOME=$HOME/.gem
export PATH=$GEM_HOME/bin:$PATH
export PATH=$HOMEBREW_PREFIX/bin:/opt/homebrew/lib/ruby/gems/3.1.0/bin:$PATH

if [ -d "/usr/local/opt/ruby/bin" ]; then
   # echo "Configure Ruby"
   export PATH=/usr/local/opt/ruby/bin:$PATH
   export PATH=`gem environment gemdir`/bin:$PATH
fi

# export PATH="$HOME/.yarn/bin:$HOME/.config/yarn/global/node_modules/.bin:$PATH"
export PATH="$(which node)":$PATH

[ -f ~/.fzf.zsh ] && source ~/.fzf.zsh

if command -v pyenv &> /dev/null; then
	export PYENV_ROOT="$HOME/.pyenv"
	[[ -d $PYENV_ROOT/bin ]] && export PATH="$PYENV_ROOT/bin:$PATH"
    eval "$(pyenv init --path)"
    eval "$(pyenv init -)"
else
  	echo "pyenv not installed, skip configuration"
fi

if [ ! -d '$HOME/google-cloud-sdk' ]; then
  # echo "Configure google-cloud-sdk"
  if [ -d '$HOME/google-cloud-sdk/bin' ]; then export PATH="$HOME/google-cloud-sdk/bin:$PATH"; fi

  # The next line enables shell command completion for gcloud.
  if [ -f '$HOME/google-cloud-sdk/completion.zsh.inc' ]; then . '$HOME/google-cloud-sdk/completion.zsh.inc'; fi

  # The next line updates PATH for the Google Cloud SDK.
  if [ -f '$HOME/google-cloud-sdk/path.zsh.inc' ]; then . '$HOME/google-cloud-sdk/path.zsh.inc'; fi
fi


if [ -d '$HOME/.console-ninja' ]; then PATH=$HOME/.console-ninja/.bin:$PATH; fi

if [ -d "$HOME/.cache/lm-studio" ]; then
  # echo "Configure LM Studio CLI"
  # Added by LM Studio CLI (lms)
  export PATH="$PATH:/Users/marc/.cache/lm-studio/bin"
fi


if [ -n "$VIRTUAL_ENV" ]; then
    source $VIRTUAL_ENV/bin/activate;
fi

if [ -f "$HOME/.cargo/env.fish" ]; then source "$HOME/.cargo/env.fish"; fi
if [ -f "$HOME/development/.env" ]; then source "$HOME/development/.env"; fi

# Add jj completion tooling
source <(jj util completion zsh)

# Enable Ctrl+R history search with preview
export FZF_DEFAULT_COMMAND='ag -g ""'

# Enable VI mode in shell to use vim like keyboard operations
set -o VI

if [ -d "$HOME/.console-ninja" ]; then
  PATH="$HOME/.console-ninja/.bin:$PATH"
fi


# bun completions
[ -s "/Users/marc/.bun/_bun" ] && source "/Users/marc/.bun/_bun"

if [ -d "$HOME/.bun" ]; then
  # echo "Configure BUN path and cli autocomplete"
  # bun completions
  [ -s "$HOME/.bun/_bun" ] && source "$HOME/.bun/_bun"
  # bun
  export BUN_INSTALL="$HOME/.bun"
  export PATH="$BUN_INSTALL/bin:$PATH"
fi

if [ -d "$ANDROID_TOOLING" ]; then
  export PATH="$ANDROID_TOOLING:$PATH"
fi

if [ -d "$HOME/.docker" ]; then
  # The following lines have been added by Docker Desktop to enable Docker CLI completions.
  fpath=($HOME/.docker/completions $fpath)
  autoload -Uz compinit
  compinit
  # End of Docker CLI completions
fi



