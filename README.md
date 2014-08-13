# Adam Eivy's Dotfiles
These are Adam Eivys's OSX shell configuration dotfiles and basic system setup preferences and software. The primary goal is to get a fresh OSX laptop up and running with my whole environment with one script. Most of the configs are best used with zsh.

![iTerm Screenshot](https://raw.githubusercontent.com/atomantic/dotfiles/master/dotfiles.png)

## Acquiring This Repo
This project contains submodules. It is suggested that you clone this into your home directory.

Note: I recommend forking this repo :)
```bash
git clone --recurse-submodules https://github.com/atomantic/dotfiles ~/.dotfiles
```


## Setup

- Edit .gitconfig and change my name to yours:
```
[user]
	name = Adam Eivy
	email = adam.eivy@disney.com
```
- Remove `source ~/Dropbox/Private/Boxes/osx/.shellaliases` from .profile or change that to a path that points to your own private aliases.

- Change your user account system shell to zsh
- Run the install script
```bash
./install.sh
```

## Additional

There are a few additional features in this repo:

- .crontab: you can `cron ~/.crontab` if you want to add my nightly cron software updates. Note that this may wake you in the morning to compatibility issues so use only if you like being on the edge :)

## Contributions
Contributions are always welcome in the form of pull requests with explanatory comments.

## Loathing, Mehs and Praise
1. Loathing should be directed into pull requests that make it better.
2. Bugs with the setup should be put as GitHub issues.
3. Mehs should be directed to /dev/null
4. Praise should be directed to [@antic](http://twitter.com/antic) or [@matthewmccull](http://twitter.com/matthewmccull) or [@mathiasbynens](https://github.com/mathiasbynens/dotfiles)