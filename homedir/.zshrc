# Path to your oh-my-zsh configuration.
export ZSH=$HOME/.dotfiles/oh-my-zsh
# if you want to use this, change your non-ascii font to Droid Sans Mono for Awesome
# POWERLEVEL9K_MODE='awesome-patched'
export ZSH_THEME="powerlevel9k/powerlevel9k"
# export ZSH_THEME="agnoster"
POWERLEVEL9K_SHORTEN_DIR_LENGTH=2
# https://github.com/bhilburn/powerlevel9k#customizing-prompt-segments
# https://github.com/bhilburn/powerlevel9k/wiki/Stylizing-Your-Prompt
POWERLEVEL9K_LEFT_PROMPT_ELEMENTS=(dir nvm vcs)
POWERLEVEL9K_RIGHT_PROMPT_ELEMENTS=(status history time)
# colorcode test
# for code ({000..255}) print -P -- "$code: %F{$code}This is how your text would look like%f"
POWERLEVEL9K_NVM_FOREGROUND='000'
POWERLEVEL9K_NVM_BACKGROUND='072'
POWERLEVEL9K_SHOW_CHANGESET=true
#export ZSH_THEME="random"

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
plugins=(colorize compleat dirpersist autojump git gulp history cp)

source $ZSH/oh-my-zsh.sh

test -e "${HOME}/.iterm2_shell_integration.zsh" && source "${HOME}/.iterm2_shell_integration.zsh"

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

# Customize to your needs...
unsetopt correct

# run fortune on new terminal :)
fortune

# zplug configuration
# Source: https://github.com/zplug/zplug
# This following settings are taken from:
# https://github.com/mrkgnao/dotfiles/blob/master/dist/zsh/.zshrc
# We have already ensured that zplug is added to the 

# Begin groups

# Eye candy ----------------------------------------

# chrissicool
zplug "chrissicool/zsh-256color"

# Syntax highlighting
# Must be loaded before history_search!
zplug "zsh-users/zsh-syntax-highlighting"

# Search in history
zplug "zsh-users/zsh-history-substring-search"

zplug "unixorn/warhol.plugin.zsh"

# frmendes/geometry
zplug "frmendes/geometry"

# Completions --------------------------------------

# NixOS completions
zplug "spwhitt/nix-zsh-completions"

# Extra completions for zsh
zplug "zsh-users/zsh-completions"

# zaw
zplug "zsh-users/zaw"

# Utilities ----------------------------------------

# Notify when commands fail or terminate after a long time
zplug "marzocchi/zsh-notify"

# joepvd/zsh-hints
zplug "joepvd/zsh-hints"

# psprint/ztrace
zplug "psprint/ztrace"

# zsh-users/zsh-autosuggestions
zplug "zsh-users/zsh-autosuggestions"

# End groups

# Make zplug manage itself!

zplug 'zplug/zplug', hook-build:'zplug --self-manage'

# End zplug config

zstyle ':notify:*' command-complete-timeout 2

zplug load
