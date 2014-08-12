#!/bin/zsh

###########################
# This script installs the dotfiles and runs all other system configuration scripts
# @author Adam Eivy
###########################


# include my library helpers for colorized echo and require_brew, etc
source ./lib.sh

export UNLINK=true

read \?"\[._.]/ - Hi. I'm going to make your OSX system better. OK?"

action "Installing OSX settings, software and dotfiles symlinks..."

#export DOTFILESDIRRELATIVETOHOME=$PWD
export DOTFILESDIRRELATIVETOHOME=.dotfiles
echo "DOTFILESDIRRELATIVETOHOME = $DOTFILESDIRRELATIVETOHOME"
pushd ~

action "formatting configs for "$(whoami)

sed -i '' 's/eivya001/'$(whoami)'/g' .zshrc;

function symlinkifne {
    action "WORKING ON: $1..."
    
    # does it exist
    if [[ -a $1 ]]; then
      warn "  $1 already exists."
      
      # If Unlink is requested
      if [ "$UNLINK" = "true" ]; then
          action "  Unlinking $1..."
          unlink $1
          
          # create the link
          action "  Symlinking $DOTFILESDIRRELATIVETOHOME/$1 to $1"
          ln -s $DOTFILESDIRRELATIVETOHOME/$1 $1
      else
        ok "  SKIPPING $1."  
      fi
    # does not exist
    else
      # create the link
      action "  Symlinking $DOTFILESDIRRELATIVETOHOME/$1 to $1"
      ln -s $DOTFILESDIRRELATIVETOHOME/$1 $1
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

popd

action "running OSX config and Brew installations"

./.osx
ok "\[._.]/ - woot! All done."