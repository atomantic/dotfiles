# \\[._.]/ - Hi, I'm the OSX bot

I will update your OSX machine with better system defaults, preferences, software configuration and even auto-install some handy development tools and apps that my developer friends find helpful.

You don't need to install or configure anything upfront! This works with a brand-new machine from the factory as well as an existing machine that you've been working with for years.

## Forget About Manual Configuration!

Don't you hate getting a new laptop or joining a new team and then spending a whole day setting up your system preferences and tools? Me too. That's why we automate; we did it once and we don't want to do have to do it again.

\\[^_^]/ - This started as Adam Eivy's OSX shell configuration dotfiles but has grown to a mutil-developer platform for machine configuration.

When I finish with your machine, you will be able to look at your command-line in full-screen mode like this:

![iTerm Screenshot](https://raw.githubusercontent.com/atomantic/dotfiles/master/img/dotfiles.png)

Check out how your shell prompt includes the full path & the working git branch!
\\[._.]/ - I'm so excited I just binaried in my pants!

## Watch me run!
![Running](http://media.giphy.com/media/5xtDarwenxEoFeIMEM0/giphy.gif)

## Running

Note: I recommend forking this repo in case you don't like anything I do and want to set your own preferences (and pull request them!)
```bash
git clone --recurse-submodules https://github.com/atomantic/dotfiles ~/.dotfiles
cd ~/.dotfiles;
./install.sh;
```

Don't have git installed yet (fresh machine)?
Just download the zip file for this project, unzip it into ~/.dotfiles then
```bash
cd ~/.dotfiles;
./install.sh;
```

> Note: running install.sh is idempotent. You can run it again and again as you add new features or software to the scripts! I'll regularly add new configurations so keep an eye on this repo as it grows and optimizes.

## ¯\\_(ツ)_/¯ Warning / Liability
> Warning: If you have existing dotfiles for configuring git, zsh, vim, etc, these will be destroyed and replaced. You might want to save any special configs and add them to a fork of this repo before installing :)
The creator of this repo is not responsible if your machine ends up in a state you are not happy with. If you are concerned, look at install.sh and osx.sh to review everything this script will do to your machine :)

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

# Release History


### <sup>v1.1.0</sup>
 * Added dotfiles backup and restore.sh script
     * you can now restore your previous dotfiles with ./restore.sh
 * prompting the user more (e.g. `brew upgrade` is now optional)

