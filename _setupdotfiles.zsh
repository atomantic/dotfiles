#!/bin/zsh

export UNLINK=true

function symlinkifne {
    echo "WORKING ON: $1"
    
    # does it exist
    if [[ -a $1 ]]; then
      echo "  WARNING: $1 already exists."
      
      # If Unlink is requested
      if [ "$UNLINK" = "true" ]; then
          unlink $1
          echo "  Unlinking $1"
          
          # create the link
          echo "  Symlinking $DOTFILESDIRRELATIVETOHOME/$1 to $1"
          ln -s $DOTFILESDIRRELATIVETOHOME/$1 $1
      else
        echo "  SKIPPING $1."  
      fi
    # does not exist
    else
      # create the link
      echo "  Symlinking $DOTFILESDIRRELATIVETOHOME/$1 to $1"
      ln -s $DOTFILESDIRRELATIVETOHOME/$1 $1
    fi
}


echo "This script must be run from the dotfiles directory"
echo "Setting up..."

#export DOTFILESDIRRELATIVETOHOME=$PWD
export DOTFILESDIRRELATIVETOHOME=.dotfiles
echo "DOTFILESDIRRELATIVETOHOME = $DOTFILESDIRRELATIVETOHOME"

pushd ~

symlinkifne .crontab
symlinkifne .gemrc
symlinkifne .gitconfig
symlinkifne .gitignore
symlinkifne .hgrc
symlinkifne .profile
symlinkifne .rvmrc
symlinkifne .screenrc
symlinkifne .shellaliases
symlinkifne .shellfn
symlinkifne .shellpaths
symlinkifne .shellvars
symlinkifne .vim
symlinkifne .vimrc
symlinkifne .zlogout
symlinkifne .zprofile
symlinkifne .zshenv
symlinkifne .zshrc

popd
