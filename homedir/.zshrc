# Performance Logging
#zmodload zsh/datetime
#setopt PROMPT_SUBST
#PS4='+$EPOCHREALTIME %N:%i> '
#logfile=$(mktemp ~/zsh_profile.XXXXXXXX)
#echo "Logging to $logfile"
#exec 3>&2 2>$logfile
#setopt XTRACE

# Path to your oh-my-zsh configuration.
export ZSH=$HOME/.dotfiles/oh-my-zsh
# if you want to use this, change your non-ascii font to Droid Sans Mono for Awesome
# POWERLEVEL9K_MODE='awesome-patched'
export ZSH_THEME="powerlevel9k/powerlevel9k"
POWERLEVEL9K_COLOR_SCHEME='light'
# export ZSH_THEME="agnoster"
POWERLEVEL9K_SHORTEN_DIR_LENGTH=2
# https://github.com/bhilburn/powerlevel9k#customizing-prompt-segments
# https://github.com/bhilburn/powerlevel9k/wiki/Stylizing-Your-Prompt
POWERLEVEL9K_LEFT_PROMPT_ELEMENTS=(dir nvm vcs)
POWERLEVEL9K_RIGHT_PROMPT_ELEMENTS=(status history time)
# colorcode test
#
textcolortest() { for code ({000..255}) print -P -- "$code: %F{$code}This is how your text would look like%f"}

POWERLEVEL9K_NVM_FOREGROUND='000'
POWERLEVEL9K_NVM_BACKGROUND='072'
POWERLEVEL9K_SHOW_CHANGESET=true
#export ZSH_THEME="random"

# Set to this to use case-sensitive completion
export CASE_SENSITIVE="true"

# disable weekly auto-update checks
# export DISABLE_AUTO_UPDATE="true"

# disable colors in ls
# export DISABLE_LS_COLORS="false"

# disable autosetting terminal title.
export DISABLE_AUTO_TITLE="true"

# Which plugins would you like to load? (plugins can be found in ~/.dotfiles/oh-my-zsh/plugins/*)
# Example format: plugins=(rails git textmate ruby lighthouse)
plugins=(colorize compleat dirpersist autojump git gulp history cp zsh-autosuggestions)

source $ZSH/oh-my-zsh.sh

test -e "${HOME}/.iterm2_shell_integration.zsh" && source "${HOME}/.iterm2_shell_integration.zsh"


# Customize to your needs...
unsetopt correct

# run fortune on new terminal :)
# fortune

# Erase when we get vim.obsession installed, also need to install tmux-resurrect
#vim () {
#    if [ -f 'Session.vim' ] && [ $# -eq 0 ]; then
#        command $vim -S Session.vim
#    else
#        command $vim "$@"
#    fi
#}

function fgr {
   fgrep -r -n $@ .
}

# NVM autoload stuff below, nvmi function should do this if not make this below into a function. doing it on every load
# is too slow
#
nvmload() {
  source /usr/local/opt/nvm/nvm.sh

  autoload -U add-zsh-hook
  load-nvmrc() {
    if [[ -f .nvmrc && -r .nvmrc ]]; then
      nvm use &> /dev/null
    elif [[ $(nvm version) != $(nvm version default)  ]]; then
      nvm use default &> /dev/null
    fi
  }
  add-zsh-hook chpwd load-nvmrc
  load-nvmrc
}

HISTFILESIZE=10000000

# Add a history repeat search using fzf
fh() {
  eval $( ([ -n "$ZSH_NAME" ] && fc -l 1 || history) | fzf +s --tac | sed 's/ *[0-9]* *//')
}

# Performance Logging
#unsetopt XTRAC#E
#exec 2>&3 3>&-
