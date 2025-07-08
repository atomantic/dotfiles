#!/usr/bin/env bash

source ./lib_sh/echos.sh

bot "installing packages from config.js..."
# node index.js
brew bundle
ok

running "cleanup homebrew"
brew cleanup --force >/dev/null 2>&1
rm -f -r /Library/Caches/Homebrew/* >/dev/null 2>&1
ok

bot "Finished brew installations."
