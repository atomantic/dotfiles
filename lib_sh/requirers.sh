#!/usr/bin/env bash

###
# convienience methods for requiring installed software
# @author Adam Eivy
###

# source ./echos.sh

# Use arch -arm64 on Apple Silicon to avoid Rosetta issues
if [[ "$(uname -m)" == "arm64" ]]; then
    BREW_CMD="arch -arm64 brew"
else
    BREW_CMD="brew"
fi

function require_apm() {
    running "checking atom plugin: $1"
    apm list --installed --bare | grep $1@ > /dev/null
    if [[ $? != 0 ]]; then
        action "apm install $1"
        apm install $1
    fi
    ok
}

function require_brew() {
    running "brew $1 $2"
    $BREW_CMD list $1 > /dev/null 2>&1
    if [[ ${PIPESTATUS[0]} != 0 ]]; then
        action "brew install $1 $2"
        $BREW_CMD install $1 $2
        if [[ $? != 0 ]]; then
            error "failed to install $1! aborting..."
            # exit -1
        fi
    fi
    ok
}

function require_cask() {
    running "brew check for cask: $1"
    $BREW_CMD list --cask $1 > /dev/null 2>&1
    if [[ ${PIPESTATUS[0]} != 0 ]]; then
        action "brew install --cask $1 $2"
        $BREW_CMD install --cask $1
        if [[ $? != 0 ]]; then
            error "failed to install $1! aborting..."
            # exit -1
        fi
    fi
    ok
}

function require_gem() {
    running "gem $1"
    if [[ $(gem list --local | grep $1 | head -1 | cut -d' ' -f1) != $1 ]]; then
        action "gem install $1"
        gem install $1
    fi
    ok
}

function require_mas() {
    running "mas $1"
    if [[ $(mas list | grep $1 | head -1 | cut -d' ' -f1) != $1 ]]; then
        action "mas install $1"
        mas install $1
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

function require_npm() {
    sourceNVM
    nvm use stable
    running "npm $*"
    npm list -g --depth 0 | grep $1@ > /dev/null
    if [[ $? != 0 ]]; then
        action "npm install -g $*"
        npm install -g $@
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
        require_brew nvm
        . ~/.bashrc
        nvm install $1
    fi
    ok
}
