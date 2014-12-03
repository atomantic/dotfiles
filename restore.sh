#!/usr/bin/env bash

###########################
# This script restores backed up dotfiles
# @author Adam Eivy
###########################

# include my library helpers for colorized echo and require_brew, etc
source ./lib.sh

bot "Do you wish to change your shell back to bash?"
read -r -p "[Y|n] " response

if [[ $response =~ ^(no|n|N) ]];then
    bot "ok, leaving shell as zsh..."
else
    bot "ok, changing shell to bash..."
    chsh -s $(which bash);ok
fi

bot "Restoring dotfiles from backup..."

pushd ~ > /dev/null 2>&1

function updatedotfile {

    if [[ -e ~/$1 ]]; then
        unlink $1;
        echo -en "project dotfile $1 removed";ok
    fi

    if [[ -e ~/.dotfiles_backup/$1 ]]; then
        mv ~/.dotfiles_backup/$1 ./
        echo -en "$1 backup restored";ok
    fi
}

updatedotfile .crontab
updatedotfile .gemrc
updatedotfile .gitconfig
updatedotfile .gitignore
updatedotfile .profile
updatedotfile .rvmrc
updatedotfile .screenrc
updatedotfile .shellaliases
updatedotfile .shellfn
updatedotfile .shellpaths
updatedotfile .shellvars
updatedotfile .vim
updatedotfile .vim/autoload
updatedotfile .vim/backup
updatedotfile .vim/bundle
updatedotfile .vim/colors
updatedotfile .vim/temp
updatedotfile .vim/.netrwhist
updatedotfile .vimrc
updatedotfile .zlogout
updatedotfile .zprofile
updatedotfile .zshenv
updatedotfile .zshrc

popd > /dev/null 2>&1

bot "Woot! All done."