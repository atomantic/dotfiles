#!/usr/bin/env bash

###########################
# This script installs the dotfiles and runs all other system configuration scripts
# @author Adam Eivy
###########################


# include my library helpers for colorized echo and require_brew, etc
source ./lib.sh

export UNLINK=false
bot "Hi. I'm going to make your OSX system better."

# read -r -p "OK? [Y/n] " response
#  if [[ ! $response =~ ^(yes|y|Y| ) ]];then
#     exit 1
#  fi

# bot "awesome. let's roll..."

#export DOTFILESDIRRELATIVETOHOME=$PWD
export DOTFILESDIRRELATIVETOHOME=.dotfiles
pushd ~ > /dev/null 2>&1

action "formatting configs for "$(whoami)"..."

sed -i '' 's/eivya001/'$(whoami)'/g' $DOTFILESDIRRELATIVETOHOME/.zshrc;

function symlinkifne {
    echo -ne "linking $1..."

    # does it exist
    if [[ -a $1 || -L $1 ]]; then

      # If Unlink is requested
      if [ "$UNLINK" = "true" ]; then
          unlink $1
          # create the link
          ln -s $DOTFILESDIRRELATIVETOHOME/$1 $1
          ok
      else
        ok
      fi
    # does not exist
    else
      # create the link
      ln -s $DOTFILESDIRRELATIVETOHOME/$1 $1
      ok
    fi
}

symlinkifne .bowerrc
symlinkifne .crontab
symlinkifne .gemrc
symlinkifne .gitconfig
symlinkifne .gitignore
symlinkifne .hgrc
symlinkifne .npmrc
symlinkifne .profile
symlinkifne .rvmrc
symlinkifne .screenrc
symlinkifne .shellaliases
symlinkifne .shellfn
symlinkifne .shellpaths
symlinkifne .shellvars
symlinkifne .vim
# in case there was already a ~/.vim
# but it doesn't contain these folders
symlinkifne .vim/autoload
symlinkifne .vim/backup
symlinkifne .vim/bundle
symlinkifne .vim/colors
symlinkifne .vim/temp
symlinkifne .vim/.netrwhist
symlinkifne .vimrc
symlinkifne .zlogout
symlinkifne .zprofile
symlinkifne .zshenv
symlinkifne .zshrc

popd > /dev/null 2>&1

./osx.sh

bot "Woot! All done."