#------------------------------------------------------------------------------
# CORE PATH
# Path to your oh-my-zsh configuration.
#------------------------------------------------------------------------------

export ZSH=$HOME/PERSONAL/LEARNING/REPOS/oh-my-zsh

#------------------------------------------------------------------------------
# THEMES
# See this great resource for more details on how to setup with iterm2
# Link: https://medium.freecodecamp.org/jazz-up-your-zsh-terminal-in-seven-steps-a-visual-guide-e81a8fd59a38
#------------------------------------------------------------------------------
export ZSH_THEME="agnoster"

#------------------------------------------------------------------------------
# GLOBAL ZSH VARIABLES
#------------------------------------------------------------------------------

# Set to this to use case-sensitive completion
export CASE_SENSITIVE="false"

# disable weekly auto-update checks
# export DISABLE_AUTO_UPDATE="true"

# disable colors in ls
# export DISABLE_LS_COLORS="true"

# disable autosetting terminal title.
export DISABLE_AUTO_TITLE="true"

#------------------------------------------------------------------------------
# PLUGINS
#------------------------------------------------------------------------------

# Which plugins would you like to load? (plugins can be found in ~/.dotfiles/oh-my-zsh/plugins/*)
# Example format: plugins=(rails git textmate ruby lighthouse)
plugins=(zsh-syntax-highlighting zsh-autosuggestions colorize compleat dirpersist autojump git gulp history cp expand-aliases)

#------------------------------------------------------------------------------
# LOAD SCRIPTS
#------------------------------------------------------------------------------

source $ZSH/oh-my-zsh.sh

autoload -U compinit zrecompile

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

# Fix for brew `command not found: z`
# Need to run `brew install z` first
# Source z.sh if available. Link: https://writequit.org/org/#13612b8d-4a0f-40da-b6ee-4fb89b3dcae3
if [ -s /usr/local/etc/profile.d/z.sh ] ; then
    source /usr/local/etc/profile.d/z.sh
fi

#------------------------------------------------------------------------------
# AUTOCOMPLETIONS
# Source: https://writequit.org/org/#13612b8d-4a0f-40da-b6ee-4fb89b3dcae3
#------------------------------------------------------------------------------

zstyle ':completion:::::' completer _complete _approximate
zstyle ':completion:*' use-cache on
zstyle ':completion:*' cache-path ~/.zsh-cache
zstyle ':completion:*' list-colors ${(s.:.)LS_COLORS}
zstyle -e ':completion:*:approximate:*' max-errors 'reply=( $(( ($#PREFIX + $#SUFFIX) / 3 )) )'
zstyle ':completion:*:descriptions' format "- %d -"
zstyle ':completion:*:corrections' format "- %d - (errors %e})"
zstyle ':completion:*:default' list-prompt '%S%M matches%s'
zstyle ':completion:*' group-name ''
zstyle ':completion:*:manuals' separate-sections true
zstyle ':completion:*:manuals.(^1*)' insert-sections true
zstyle ':completion:*' verbose yes
zstyle ':completion:*' file-list list=20 insert=10

#------------------------------------------------------------------------------
# ZSH OPTIONS
# Main Source: http://zsh.sourceforge.net/Doc/Release/Options.html
#------------------------------------------------------------------------------

setopt multios               # allow pipes to be split/duplicated
# ^^ try this: cat foo.clj > >(fgrep java | wc -l) > >(fgrep copy | wc -l)
setopt globdots

#------------------------------------------------------------------------------
# ZPLUG
# zplug configuration
# Source: https://github.com/zplug/zplug
# Link: https://github.com/mrkgnao/dotfiles/blob/master/dist/zsh/.zshrc
#------------------------------------------------------------------------------

# Install zplug if it does not already exist
if [ ! -d "/Users/shamindras/.zplug" ] 
then
    curl -sL --proto-redir -all,https https://raw.githubusercontent.com/zplug/installer/master/installer.zsh | zsh
fi

# Load zplug
source ~/.zplug/init.zsh
zplug load

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
