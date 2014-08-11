# Adam Eivy's Dotfiles
These are Adam Eivys's OSX shell configuration dotfiles. The primary goal is to increase CLI productivity on Mac OSX, though many scripts run just fine on any POSIX implementation. 

## Focus
The focus is on Zshell support, but there are plenty of cross-platform scripts in here. There are a few bash-specific ones.

## Inspirations
The contents of this repo have been partly invented from scratch, partly inspired by open source projects, and partly refactored from snippets from colleagues and friends. Many are attributed.
Most of these scripts are based on or ported from [Matthew Mccullough's dotfiles](https://github.com/matthewmccullough/dotfiles) and [mathiasbynens dotfiles](https://github.com/mathiasbynens/dotfiles)

## Acquiring This Repo
This project contains submodules. It is suggested that you clone this into your home directory.

Note: I recommend forking this repo :)
```bash
git clone --recurse-submodules https://github.com/atomantic/dotfiles ~/.dotfiles
```


## Setup

Before you setup, edit .gitconfig and change my name to yours:
```
[user]
	name = Adam Eivy
	email = adam.eivy@disney.com
```

You will also want to remove `source ~/Dropbox/Private/Boxes/osx/.shellaliases` from .profile

There is a set up script that establishes the symlinks in your home directory. Run this once.

* For ZShell
```bash
./_setupdotfiles.zsh
```

## Contributions
Contributions are always welcome in the form of pull requests with explanatory comments.

## Loathing, Mehs and Praise
1. Loathing should be directed into pull requests that make it better.
2. Bugs with the setup should be put as GitHub issues.
3. Mehs should be directed to /dev/null
4. Praise should be directed to [@antic](http://twitter.com/antic) or [@matthewmccull](http://twitter.com/matthewmccull) or [@mathiasbynens](https://github.com/mathiasbynens/dotfiles)