#!/usr/bin/env bash

###########################
# This script installs the dotfiles and runs all other system configuration scripts
# @author Adam Eivy
###########################

DEFAULT_EMAIL="adam.eivy@disney.com"
DEFAULT_GITHUBUSER="atomantic"
DEFAULT_NAME="Adam Eivy"
DEFAULT_USERNAME="antic"


# include my library helpers for colorized echo and require_brew, etc
source ./lib.sh

# make a backup directory for overwritten dotfiles
if [[ ! -e ~/.dotfiles_backup ]]; then
    mkdir ~/.dotfiles_backup
fi

bot "Hi. I'm going to make your OSX system better. But first, I need to configure this project based on your info so you don't check in files to github as Adam Eivy from here on out :)"

fullname=`osascript -e "long user name of (system info)"`

if [[ -n "$fullname" ]];then
  lastname=$(echo $fullname | awk '{print $2}');
  firstname=$(echo $fullname | awk '{print $1}');
fi

# me=`dscl . -read /Users/$(whoami)`

if [[ -z $lastname ]]; then
  lastname=`dscl . -read /Users/$(whoami) | grep LastName | sed "s/LastName: //"`
fi
if [[ -z $firstname ]]; then
  firstname=`dscl . -read /Users/$(whoami) | grep FirstName | sed "s/FirstName: //"`
fi
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
  read -r -p "What is your email? [$DEFAULT_EMAIL] " email
  if [[ ! $email ]];then
    email=$DEFAULT_EMAIL
  fi
fi

grep 'user = atomantic' .gitconfig
if [[ $? = 0 ]]; then
    read -r -p "What is your github.com username? [$DEFAULT_GITHUBUSER]" githubuser
fi
if [[ ! $githubuser ]];then
  githubuser=$DEFAULT_GITHUBUSER
fi

running "replacing items in .gitconfig with your info ($COL_YELLOW$fullname, $email, $githubuser$COL_RESET)"

# test if gnu-sed or osx sed

sed -i "s/$DEFAULT_NAME/$firstname $lastname/" .gitconfig > /dev/null 2>&1 | true
if [[ ${PIPESTATUS[0]} != 0 ]]; then
  echo
  running "looks like you are using OSX sed rather than gnu-sed, accommodating"
  sed -i '' "s/$DEFAULT_NAME/$firstname $lastname/" .gitconfig;
  sed -i '' 's/'$DEFAULT_EMAIL'/'$email'/' .gitconfig;
  sed -i '' 's/'$DEFAULT_GITHUBUSER'/'$githubuser'/' .gitconfig;
  sed -i '' 's/'$DEFAULT_USERNAME'/'$(whoami)'/g' .zshrc;ok
else
  echo
  bot "looks like you are already using gnu-sed. woot!"
  sed -i 's/'$DEFAULT_EMAIL'/'$email'/' .gitconfig;
  sed -i 's/'$DEFAULT_GITHUBUSER'/'$githubuser'/' .gitconfig;
  sed -i 's/'$DEFAULT_USERNAME'/'$(whoami)'/g' .zshrc;ok
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

pushd ~ > /dev/null 2>&1

bot "creating symlinks for project dotfiles..."

symlinkifne .crontab
symlinkifne .gemrc
symlinkifne .gitconfig
symlinkifne .gitignore
symlinkifne .profile
symlinkifne .screenrc
symlinkifne .shellaliases
symlinkifne .shellfn
symlinkifne .shellpaths
symlinkifne .shellvars
symlinkifne .tmux.conf
symlinkifne .vim
symlinkifne .vimrc
symlinkifne .zlogout
symlinkifne .zprofile
symlinkifne .zshenv
symlinkifne .zshrc

popd > /dev/null 2>&1

./osx.sh

bot "Woot! All done."
