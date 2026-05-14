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
  local package=$1
  local options=${2:-}
  local installed_now=0

  running "brew $package${options:+ ($options)}"
  if ! brew list "$package" >/dev/null 2>&1; then
    action "brew install $package"
    brew install "$package"
    if [[ $? != 0 ]]; then
      error "failed to install $package! aborting..."
      return 1
    fi
    installed_now=1
  fi

  if [[ "$options" == *"link: false"* ]]; then
    brew unlink "$package" >/dev/null 2>&1 || true
  fi

  if [[ $installed_now -eq 1 && "$options" == *"restart_service: :changed"* ]]; then
    brew services restart "$package" || warn "failed to restart service for $package"
  fi
  ok
}

function require_cask() {
  local package=$1

  running "brew cask $package"
  if ! brew list --cask "$package" >/dev/null 2>&1; then
    action "brew install --cask $package"
    brew install --cask "$package"
    if [[ $? != 0 ]]; then
      error "failed to install $package! aborting..."
      return 1
    fi
  fi
  ok
}

function require_gem() {
  running "gem $1"
  if [[ $(gem list --local | grep $1 | head -1 | cut -d' ' -f1) != $1 ]]; then
    action "gem install $1"
    gem install $1 || return 1
  fi
  ok
}

function require_mas() {
  running "mas $1 ($2)"
  if [[ $(mas list | grep $2 | head -1 | cut -d' ' -f1) != $2 ]]; then
    action "mas install $2"
    mas install $2 || return 1
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
  sourceNVM || return 1
  nvm use stable || nvm use default || true
  running "npm $*"
  npm list -g --depth 0 | grep $1@ >/dev/null
  if [[ $? != 0 ]]; then
    action "npm install -g $*"
    npm install -g "$@" || return 1
  fi
  ok
}

function sourceNVM() {
  export NVM_DIR="$HOME/.nvm"
  local nvm_script
  nvm_script="$(brew --prefix nvm)/nvm.sh"
  if [[ -r "$nvm_script" ]]; then
    source "$nvm_script"
  else
    error "nvm is installed but $nvm_script is not readable"
    return 1
  fi
}

function require_nvm() {
  local version=${1:-}

  if [[ -z "$version" && -r ".nvmrc" ]]; then
    version=$(<.nvmrc)
  elif [[ -z "$version" && -r "./homedir/.nvmrc" ]]; then
    version=$(<./homedir/.nvmrc)
  fi

  if [[ -z "$version" ]]; then
    warn "No Node version provided and no .nvmrc found; skipping Node install"
    return 0
  fi

  mkdir -p "$HOME/.nvm"
  cp "$(brew --prefix nvm)/nvm-exec" "$HOME/.nvm/"
  sourceNVM || return 1

  nvm install "$version" || return 1
  nvm alias default "$version" >/dev/null
  nvm use "$version" >/dev/null
  ok
}

function require_tap() {
    running "brew tap $1"
    brew tap "$@"
    ok
}

function require_vscode() {
    running "vscode extension $1"
    code --list-extensions | grep -i "$1" >/dev/null
    if [[ $? != 0 ]]; then
        action "code --install-extension $1"
        code --install-extension "$@" || return 1
    fi
    ok
}
