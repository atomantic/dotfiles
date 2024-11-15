# rosetta terminal setup
if [ $(arch) = "i386" ]; then
    alias brew86="/usr/local/bin/brew"
    alias pyenv86="arch -x86_64 pyenv"
    eval "$(/usr/local/bin/brew shellenv)"
 	export PATH="/usr/local/opt/ruby/bin:$PATH"
else
    # Fig pre block. Keep at the top of this file.
    eval "$(/opt/homebrew/bin/brew shellenv)"
	export PATH="/opt/homebrew/opt/ruby/bin:$PATH"
fi


# Add jetbrains command line
export PATH="$HOME/Library/Application Support/JetBrains/Toolbox/scripts:$PATH"
export PATH=/opt/homebrew/bin:/usr/local/bin:$PATH

# Ruby Paths
export GEM_HOME=$HOME/.gem
export PATH=$GEM_HOME/bin:$PATH
export PATH=$HOMEBREW_PREFIX/bin:/opt/homebrew/lib/ruby/gems/3.1.0/bin:$PATH

# AI Dev
if [ -f "$HOME/.private_vars.inc" ]; then source "$HOME/.private_vars.inc"; fi

# General

if [ -d '$HOME/Applications/Android\ Studio.app' ]; then
	export JAVA_HOME=$HOME/Applications/Android\ Studio.app/Contents/jbr/Contents/Home
	export ANDROID_HOME=$HOME/Library/Android/sdk

	export PATH="$ANDROID_HOME/tools:ANDROID_HOME/tools/bin:ANDROID_HOME/platform-tools:$ANDROID_HOME/cmdline-tools/latest/bin:$PATH"
fi

if [ $(arch) = "i386" ]; then
	export PATH="/usr/local/opt/openjdk@/bin:$PATH"
else
	export PATH="/opt/homebrew/opt/openjdk@/bin:$PATH"
fi

# Path to your oh-my-zsh configuration.
export ZSH=$HOME/.dotfiles/oh-my-zsh
# if you want to use this, change your non-ascii font to Droid Sans Mono for Awesome
# POWERLEVEL9K_MODE='awesome-patched'
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
plugins=(colorize compleat dirpersist autojump git gulp history cp kubectl kubectx)

source $ZSH/oh-my-zsh.sh


if [[ -f /opt/homebrew/opt/nvm/nvm.sh ]]; then
    source /opt/homebrew/opt/nvm/nvm.sh --no-use
fi

export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" # This loads nvm
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion" # This loads nvm bash_completion

autoload -U add-zsh-hook
 export NVM_DIR="$HOME/.nvm"
  [ -s "/usr/local/opt/nvm/nvm.sh" ] && \. "/usr/local/opt/nvm/nvm.sh"  # This loads nvm
  [ -s "/usr/local/opt/nvm/etc/bash_completion.d/nvm" ] && \. "/usr/local/opt/nvm/etc/bash_completion.d/nvm"  # This loads nvm bash_completion
load-nvmrc() {
  if [[ -f .nvmrc && -r .nvmrc ]]; then
    nvm use &> /dev/null
  else
    nvm use stable
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

if [ -d "/usr/local/opt/ruby/bin" ]; then
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
  if [ -d '$HOME/google-cloud-sdk/bin' ]; then export PATH="$HOME/google-cloud-sdk/bin:$PATH"; fi

  # The next line enables shell command completion for gcloud.
  if [ -f '$HOME/google-cloud-sdk/completion.zsh.inc' ]; then . '$HOME/google-cloud-sdk/completion.zsh.inc'; fi

  # The next line updates PATH for the Google Cloud SDK.
  if [ -f '$HOME/google-cloud-sdk/path.zsh.inc' ]; then . '$HOME/google-cloud-sdk/path.zsh.inc'; fi
fi

function cd() {
  builtin cd "$@"

  if [[ -z "$VIRTUAL_ENV" ]] ; then
    ## If env folder is found then activate the vitualenv
      if [[ -d ./venv ]] ; then
        source ./venv/bin/activate
      fi
  else
    ## check the current folder belong to earlier VIRTUAL_ENV folder
    # if yes then do nothing
    # else deactivate
      parentdir="$(dirname "$VIRTUAL_ENV")"
      if [[ "$PWD"/ != "$parentdir"/* ]] ; then
        deactivate
      fi
  fi
}

if [ -d '$HOME/.console-ninja' ]; then PATH=$HOME/.console-ninja/.bin:$PATH; fi
