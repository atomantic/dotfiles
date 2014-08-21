#!/usr/bin/env bash

###########################
# This script installs the dotfiles and runs all other system configuration scripts
# @author Adam Eivy
###########################


# include my library helpers for colorized echo and require_brew, etc
source ./lib.sh

export UNLINK=false
bot "Hi. I'm going to make your OSX system better. But first, I need to configure this project based on your info so you don't check in files to github as Adam Eivy from here on out :)"

#fullname=`osascript -e "long user name of (system info)"`
#me=`dscl . -read /Users/$(whoami)`

lastname=`dscl . -read /Users/$(whoami) | grep LastName | sed "s/LastName: //"`
firstname=`dscl . -read /Users/$(whoami) | grep FirstName | sed "s/FirstName: //"`
email=`dscl . -read /Users/$(whoami)  | grep EMailAddress | sed "s/EMailAddress: //"`

if [[ ! "$firstname" ]];then
  response='n'
else
  echo -e "I see that your full name is $COL_YELLOW$firstname $lastname$COL_RESET"
  read -r -p "Is this correct? [Y|n] " response
fi

if [[ $response =~ ^(no|n|N) ]];then
	read -r -p "What is your first name? " firstname
	read -r -p "What is your last name? " lastname
fi
fullname="$firstname $lastname"

bot "Great $fullname, "

if [[ ! $email ]];then
  response='n'
else
  echo -e "The best I can make out, your email address is $COL_YELLOW$email$COL_RESET"
  read -r -p "Is this correct? [Y|n] " response
fi

if [[ $response =~ ^(no|n|N) ]];then
	read -r -p "What is your email? " email
fi

read -r -p "What is your github.com username? " githubuser

running "replacing items in .gitconfig with your info ($COL_YELLOW$fullname, $email, $githubuser$COL_RESET)"

# test if gnu-sed or osx sed

sed -i 's/Adam Eivy/'$firstname' '$lastname'/' .gitconfig > /dev/null 2>&1 | true
if [[ ${PIPESTATUS[0]} != 0 ]]; then
  echo
  running "looks like you are using OSX sed rather than gnu-sed, accommodating"
  sed -i '' 's/Adam Eivy/'$firstname' '$lastname'/' .gitconfig;
  sed -i '' 's/adam.eivy@disney.com/'$email'/' .gitconfig;
  sed -i '' 's/atomantic/'$githubuser'/' .gitconfig;
  sed -i '' 's/antic/'$(whoami)'/g' .zshrc;ok
else
  echo
  bot "looks like you are already using gnu-sed. woot!"
  sed -i 's/Adam Eivy/'$firstname' '$lastname'/' .gitconfig;
  sed -i 's/adam.eivy@disney.com/'$email'/' .gitconfig;
  sed -i 's/atomantic/'$githubuser'/' .gitconfig;
  sed -i 's/antic/'$(whoami)'/g' .zshrc;ok
fi



# read -r -p "OK? [Y/n] " response
#  if [[ ! $response =~ ^(yes|y|Y| ) ]];then
#     exit 1
#  fi

# bot "awesome. let's roll..."

echo $0 | grep zsh > /dev/null 2>&1 | true
if [[ ${PIPESTATUS[0]} != 0 ]]; then
	running "changing your login shell to zsh"
	chsh -s $(which zsh);ok
else
	bot "looks like you are already using zsh. woot!"
fi

#export DOTFILESDIRRELATIVETOHOME=$PWD
export DOTFILESDIRRELATIVETOHOME=.dotfiles
pushd ~ > /dev/null 2>&1

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

symlinkifne .crontab
symlinkifne .gemrc
symlinkifne .gitconfig
symlinkifne .gitignore
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