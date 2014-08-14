# \\[._.]/ - Hi, I'm the OSX bot

I will update your OSX machine with better system defaults, preferences, software configuration and even auto-install some handy development tools and apps that my developer friends find helpful.

\\[^_^]/ - This started as Adam Eivy's OSX shell configuration dotfiles and setup so blame him if you don't like anything I do to your machine.

When I finish with your machine, you will be able to look at your command-line like this:

![iTerm Screenshot](https://raw.githubusercontent.com/atomantic/dotfiles/master/img/dotfiles.png)

Note that your shell includes the full path + the git branch!
\\[._.]/ - I'm so excited I just binaried in my pants!

## Acquiring This Repo
This project contains submodules. Clone this into your home directory.

Note: I recommend forking this repo in case you don't like anything I do and want to set your own preferences (and pull request them!)
```bash
git clone --recurse-submodules https://github.com/atomantic/dotfiles ~/.dotfiles
```
## Setup

> \\[._.]/ - Hey, wouldn't it be cool if I walked you through these next steps on the terminal?
> Maybe you want to fork this repo and pull-request that to Adam! (or he might get to it eventually)

- Edit .gitconfig and change Adam's name to yours:
```
[user]
	name = Adam Eivy
	email = adam.eivy@disney.com
```
- Adam also has some private shell aliases in Dropbox so you will want to remove `source ~/Dropbox/Private/Boxes/osx/.shellaliases` from .profile or change that to a path that points to your own private aliases.

- Change your user account system shell to zsh
- Run the install script
```bash
./install.sh
```

> Note: running install.sh is idempotent. You can run it again and again as you add new features or software to the scripts!

## Additional

There are a few additional features in this repo:

- .crontab: you can `cron ~/.crontab` if you want to add my nightly cron software updates.
> \\[0_0]/ - Note that this may wake you in the morning to compatibility issues so use only if you like being on the edge

## Contributions
Contributions are always welcome in the form of pull requests with explanatory comments.

Please refer to the [Contributor Covenant](https://github.com/atomantic/dotfiles/blob/master/CODE_OF_CONDUCT.md)

## Loathing, Mehs and Praise
1. Loathing should be directed into pull requests that make it better. woot.
2. Bugs with the setup should be put as GitHub issues.
3. Mehs should be > /dev/null
4. Praise should be directed to [@antic](http://twitter.com/antic) or [@matthewmccull](http://twitter.com/matthewmccull) or [@mathiasbynens](https://github.com/mathiasbynens/dotfiles)