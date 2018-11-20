# Path to your oh-my-zsh configuration.
POWERLEVEL9K_MODE='nerdfont-complete'
export ZSH=$HOME/.dotfiles/oh-my-zsh
export ZSH_THEME="powerlevel9k/powerlevel9k"
# if you want to use this, change your non-ascii font to Droid Sans Mono for Awesome
# POWERLEVEL9K_MODE='awesome-patched'
# export ZSH_THEME="powerlevel9k/powerlevel9k"
POWERLEVEL9K_SHORTEN_DIR_LENGTH=2
POWERLEVEL9K_SHORTEN_FOLDER_MARKER="code.espn.com"
# POWERLEVEL9K_SHORTEN_STRATEGY="truncate_with_package_name"
POWERLEVEL9K_DIR_SHOW_WRITABLE=true
# https://github.com/bhilburn/powerlevel9k#customizing-prompt-segments
# https://github.com/bhilburn/powerlevel9k/wiki/Stylizing-Your-Prompt
# POWERLEVEL9K_LEFT_PROMPT_ELEMENTS=(dir nvm vcs)
# POWERLEVEL9K_RIGHT_PROMPT_ELEMENTS=(status history time)
# colorcode test
# for code ({000..255}) print -P -- "$code: %F{$code}This is how your text would look like%f"
# POWERLEVEL9K_NVM_FOREGROUND='000'
# POWERLEVEL9K_NVM_BACKGROUND='072'
# POWERLEVEL9K_SHOW_CHANGESET=true
#export ZSH_THEME="random"
POWERLEVEL9K_USER_BACKGROUND='black'
POWERLEVEL9K_CUSTOM_WIFI_SIGNAL="zsh_wifi_signal"
POWERLEVEL9K_CUSTOM_WIFI_SIGNAL_BACKGROUND="blue"
POWERLEVEL9K_CUSTOM_WIFI_SIGNAL_FOREGROUND="yellow"
POWERLEVEL9K_USER_ICON="" #
POWERLEVEL9K_ROOT_ICON="#"
POWERLEVEL9K_SUDO_ICON="" #
POWERLEVEL9K_DIR_OMIT_FIRST_CHARACTER=false
zsh_wifi_signal(){
        local output=$(/System/Library/PrivateFrameworks/Apple80211.framework/Versions/A/Resources/airport -I)
        local airport=$(echo $output | grep 'AirPort' | awk -F': ' '{print $2}')

        if [ "$airport" = "Off" ]; then
                local color='%F{yellow}'
                echo -n "%{$color%}Wifi Off"
        else
                local ssid=$(echo $output | grep ' SSID' | awk -F': ' '{print $2}')
                local speed=$(echo $output | grep 'lastTxRate' | awk -F': ' '{print $2}')
                local color='%F{yellow}'

                [[ $speed -gt 100 ]] && color='%F{green}'
                [[ $speed -lt 50 ]] && color='%F{red}'

                echo -n "%{$color%}$ssid $speed Mb/s%{%f%}" # removed char not in my PowerLine font
        fi
}

export POWERLEVEL9K_LEFT_PROMPT_ELEMENTS=(os_icon user dir dir_writable status)
export POWERLEVEL9K_RIGHT_PROMPT_ELEMENTS=(vcs custom_wifi_signal battery)
POWERLEVEL9K_PROMPT_ON_NEWLINE=true
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
plugins=(encode64 colorize compleat dirpersist zsh-navigation-tools autojump battery npm brew common-aliases git-extras grunt zsh_reload git gulp history cp branch yarn)

source $ZSH/oh-my-zsh.sh
. ~/zsh/env
. ~/zsh/aliases

test -e "${HOME}/.iterm2_shell_integration.zsh" && source "${HOME}/.iterm2_shell_integration.zsh"


# Color completion for some things.
# http://linuxshellaccount.blogspot.com/2008/12/color-completion-using-zsh-modules-on.html
zstyle ':completion:*' list-colors ${(s.:.)LS_COLORS}

# formatting and messages
# http://www.masterzen.fr/2009/04/19/in-love-with-zsh-part-one/
zstyle ':completion:*' verbose yes
zstyle ':completion:*:descriptions' format "$fg[yellow]%B--- %d%b"
zstyle ':completion:*:messages' format '%d'
zstyle ':completion:*:warnings' format 'Too bad there is nothing'
zstyle ':completion:*:corrections' format '%B%d (errors: %e)%b'
zstyle ':completion:*' group-name ''



# source /usr/local/opt/nvm/nvm.sh

autoload -U add-zsh-hook
# load-nvmrc() {
#   if [[ -f .nvmrc && -r .nvmrc ]]; then
#     nvm use &> /dev/null
#   elif [[ $(nvm version) != $(nvm version default)  ]]; then
#     nvm use default &> /dev/null
#   fi
# }
# add-zsh-hook chpwd load-nvmrc
# load-nvmrc

# Customize to your needs...
unsetopt correct

# run fortune on new terminal :)
fortune

export COMPLETION_WAITING_DOTS=true