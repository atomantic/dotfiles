#!/usr/bin/env bash

###
# some bash library helpers
# @author Adam Eivy
###

# Colors
ESC_SEQ="\x1b["
COL_RESET=$ESC_SEQ"39;49;00m"
COL_RED=$ESC_SEQ"31;01m"
COL_GREEN=$ESC_SEQ"32;01m"
COL_YELLOW=$ESC_SEQ"33;01m"
COL_BLUE=$ESC_SEQ"34;01m"
COL_MAGENTA=$ESC_SEQ"35;01m"
COL_CYAN=$ESC_SEQ"36;01m"

function ok() {
    echo -e "$COL_GREEN[ok]$COL_RESET "$1
}

function bot() {
    echo -e "\n$COL_GREEN\[._.]/$COL_RESET - "$1
}

function running() {
    echo -en "$COL_YELLOW ⇒ $COL_RESET"$1": "
}

function action() {
    echo -e "\n$COL_YELLOW[action]:$COL_RESET\n ⇒ $1..."
}

function warn() {
    echo -e "$COL_YELLOW[warning]$COL_RESET "$1
}

function error() {
    echo -e "$COL_RED[error]$COL_RESET "$1
}

function require_cask() {
    running "brew cask $1"
    brew cask list $1 > /dev/null 2>&1 | true
    if [[ ${PIPESTATUS[0]} != 0 ]]; then
        action "brew cask install $1 $2"
        brew cask install $1
        if [[ $? != 0 ]]; then
            error "failed to install $1! aborting..."
            # exit -1
        fi
    fi
    ok
}

function require_brew() {
    running "brew $1 $2"
    brew list $1 > /dev/null 2>&1 | true
    if [[ ${PIPESTATUS[0]} != 0 ]]; then
        action "brew install $1 $2"
        brew install $1 $2
        if [[ $? != 0 ]]; then
            error "failed to install $1! aborting..."
            # exit -1
        fi
    fi
    ok
}

function require_node(){
    running "node -v"
    node -v
    if [[ $? != 0 ]]; then
        action "node not found, installing via homebrew"
        brew install node
    fi
    ok
}

function require_gem() {
    running "gem $1"
    if [[ $(gem list --local | grep $1 | head -1 | cut -d' ' -f1) != $1 ]];
        then
            action "gem install $1"
            gem install $1
    fi
    ok
}

function require_npm() {
    sourceNVM
    nvm use stable
    running "npm $1"
    npm list -g --depth 0 | grep $1@ > /dev/null
    if [[ $? != 0 ]]; then
        action "npm install -g $1"
        npm install -g $1
    fi
    ok
}

function require_apm() {
    running "checking atom plugin: $1"
    apm list --installed --bare | grep $1@ > /dev/null
    if [[ $? != 0 ]]; then
        action "apm install $1"
        apm install $1
    fi
    ok
}

function sourceNVM(){
    export NVM_DIR=~/.nvm
    source $(brew --prefix nvm)/nvm.sh
}


function require_nvm() {
    mkdir -p ~/.nvm
    cp $(brew --prefix nvm)/nvm-exec ~/.nvm/
    sourceNVM
    nvm install $1
    if [[ $? != 0 ]]; then
        action "installing nvm"
        curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.25.4/install.sh | bash
        . ~/.bashrc
        nvm install $1
    fi
    nvm use $1
    ok
}

function promptSudo(){
    # Ask for the administrator password upfront
    bot "I need you to enter your sudo password so I can install some things:"
    sudo -v

    # Keep-alive: update existing sudo time stamp until the script has finished
    while true; do sudo -n true; sleep 60; kill -0 "$$" || exit; done 2>/dev/null &
}


function symlinkifne {
    running "$1"

    if [[ -e $1 ]]; then
        # file exists
        if [[ -L $1 ]]; then
            # it's already a simlink (could have come from this project)
            echo -en '\tsimlink exists, skipped\t';ok
            return
        fi
        # backup file does not exist yet
        if [[ ! -e ~/.dotfiles_backup/$1 ]];then
            mv $1 ~/.dotfiles_backup/
            echo -en 'backed up saved...';
        fi
    fi
    # create the link
    ln -s ~/.dotfiles/$1 $1
    echo -en '\tlinked';ok
}
