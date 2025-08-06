#!/usr/bin/env bash

###
# convienience methods for requiring installed software
# @author Adam Eivy
###

# source ./echos.sh

function require_apm() {
  running "checking atom plugin: $1"
  apm list --installed --bare | grep $1@ >/dev/null
  if [[ $? != 0 ]]; then
    action "apm install $1"
    apm install $1
  fi
  ok
}

function require_brew() {
  running "brew $*"
  brew list $1 >/dev/null 2>&1 | true
  if [[ ${PIPESTATUS[0]} != 0 ]]; then
    action "brew install $*"
    brew install "$@"
    if [[ $? != 0 ]]; then
      error "failed to install $1! aborting..."
      # exit -1
    fi
  fi
  ok
}

function require_cask() {
  running "brew cask $*"
  brew cask list $1 >/dev/null 2>&1 | true
  if [[ ${PIPESTATUS[0]} != 0 ]]; then
    action "brew cask install $*"
    brew install --cask "$@"
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
  running "mas $1 ($2)"
  if [[ $(mas list | grep $2 | head -1 | cut -d' ' -f1) != $2 ]]; then
    action "mas install $2"
    mas install $2
  fi
  ok
}

function require_node() {
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
  npm list -g --depth 0 | grep $1@ >/dev/null
  if [[ $? != 0 ]]; then
    action "npm install -g $*"
    npm install -g "$@"
  fi
  ok
}

function sourceNVM() {
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
    . ~/.zshrc
    nvm install $1
  fi
  ok
}

function require_tap() {
    running "brew tap $1"
    brew tap "$@"
    ok
}

function require_vscode() {
    running "vscode extension $1"
    code --list-extensions | grep -i $1 >/dev/null
    if [[ $? != 0 ]]; then
        action "code --install-extension $1"
        code --install-extension "$@"
    fi
    ok
}
