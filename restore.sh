#!/usr/bin/env bash

###########################
# This script restores backed up dotfiles
# @author Adam Eivy
###########################

# include my library helpers for colorized echo and require_brew, etc
source ./lib_sh/echos.sh
source ./lib_sh/requirers.sh

if [[ -z $1 ]]; then
  error "you need to specify a backup folder date. Take a look in ~/.dotfiles_backup/ to see which backup date you wish to restore."
  exit 1
fi


bot "Do you wish to change your shell back to bash?"
read -r -p "[Y|n] " response

if [[ $response =~ ^(no|n|N) ]];then
    bot "ok, leaving shell as zsh..."
else
    bot "ok, changing shell to bash..."
    chsh -s $(which bash);ok
fi

bot "Restoring dotfiles from backup..."

pushd ~/.dotfiles_backup/$1

for file in .*; do
  if [[ $file == "." || $file == ".." ]]; then
    continue
  fi

  running "~/$file"
  if [[ -e ~/$file ]]; then
      unlink $file;
      echo -en "project dotfile $file unlinked";ok
  fi

  if [[ -e ./$file ]]; then
      mv ./$file ./
      echo -en "$1 backup restored";ok
  fi
  echo -en '\done';ok
done

popd

bot "Woot! All done."
