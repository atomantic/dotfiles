# \\[._.]/ - Hi, I'm the MacOS bot

I will update your MacOS machine with Better™ system defaults, preferences, software configuration and even auto-install some handy development tools and apps that my developer friends find helpful.

You don't need to install or configure anything upfront! This works with a brand-new machine from the factory as well as an existing machine that you've been working with for years.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->

- [Forget About Manual Configuration!](#forget-about-manual-configuration)
- [Watch me run!](#watch-me-run)
- [Installation](#installation)
  - [Restoring Dotfiles](#restoring-dotfiles)
- [3.x.x+ Upgrade Instructions!](#3xx-upgrade-instructions)
- [Additional](#additional)
  - [VIM as IDE](#vim-as-ide)
  - [Crontab](#crontab)
  - [Remap Caps-Lock](#remap-caps-lock)
- [Settings](#settings)
  - [Prompt Driven Configuration](#prompt-driven-configuration)
  - [Security](#security)
  - [General System Changes](#general-system-changes)
  - [Finder Configs](#finder-configs)
  - [Dock & Dashboard](#dock--dashboard)
  - [Configuring Hot Corners](#configuring-hot-corners)
  - [Spotlight](#spotlight)
  - [iTerm2](#iterm2)
  - [Time Machine](#time-machine)
  - [Activity Monitor](#activity-monitor)
  - [Dashboard, iCal, TextEdit, and Disk Utility](#dashboard-ical-textedit-and-disk-utility)
  - [Mac App Store](#mac-app-store)
  - [Messages](#messages)
- [Software Installation](#software-installation)
- [License](#license)
- [Contributions](#contributions)
- [Loathing, Mehs and Praise](#loathing-mehs-and-praise)
- [Author](#author)
- [¯\\_(ツ)_/¯ Warning / Liability](#%C2%AF%5C%5C_%E3%83%84_%C2%AF-warning--liability)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# Forget About Manual Configuration!

Don't you hate getting a new laptop or joining a new team and then spending a whole day setting up your system preferences and tools? Me too. That's why we automate; we did it once and we don't want to have to do it again.

\\[^_^]/ - This started as [Adam Eivy](http://adameivy.com)'s MacOS shell configuration dotfiles but has grown to a multi-developer platform for machine configuration.

When I finish with your machine, you will be able to look at your command-line in full-screen mode like this (running iTerm):

![iTerm Screenshot](img/term.png)

Check out how your shell prompt includes the full path, node.js version & the working git branch along with a lot of other info!
We use powerlevel9k for command prompt, so customization of what you want is easily changable in `./.zshrc`
The top terminal is using vim as a full replacement IDE.
The bottom left two are git terminals.
The bottom right is running `vtop`

To launch fullscreen, hit `Command + Enter` in iTerm, then use `Command + d` and `Command + D` to create split panes.

\\[._.]/ - I'm so excited I just binaried in my pants!

# Watch me run!

![Running](http://media.giphy.com/media/5xtDarwenxEoFeIMEM0/giphy.gif)

# Installation

> Note: I recommend forking this repo in case you don't like anything I do and want to set your own preferences (and pull request them!)

> ☢️ REVIEW WHAT THIS SCRIPT DOES PRIOR TO RUNNING
> It's always a good idea to review arbitrary code from the internet before running it on your machine with sudo power!
> You are responsible for everything this script does to your machine (see LICENSE)
> The creator of this repo has a high tolerance for nuking his machine and starting over. If you have a low tolerance for this, proceed with caution.

```bash
git clone --recurse-submodules https://github.com/atomantic/dotfiles ~/.dotfiles
cd ~/.dotfiles;
# run this using terminal (not iTerm, lest iTerm settings get discarded on exit)
./install.sh
```

- When it finishes, open iTerm and press `Command + ,` to open preferences. Under Profiles > Colors, select "Load Presets" and choose the `Solarized Dark Patch` scheme. If it isn't there for some reason, import it from `~/.dotfiles/configs` -- you may also need to select the `Hack` font and check the box for non-ascii font and set to `Roboto Mono For Powerline` (I've had mixed results for automating these settings--love a pull request that improves this)
- I've also found that you need to reboot before fast key repeat will be enabled

> Note: if you have problems cloning the submodules behind proxy, you can use this command to convert `git://` to `https://`: `git config --global url.https://github.com/.insteadOf git://github.com/`

> Note: running install.sh is idempotent. You can run it again and again as you add new features or software to the scripts! I'll regularly add new configurations so keep an eye on this repo as it grows and optimizes.

## Restoring Dotfiles

If you have existing dotfiles for configuring git, zsh, vim, etc, these will be backed-up into `~/.dotfiles_backup/$(date +"%Y.%m.%d.%H.%M.%S")` and replaced with the files from this project. You can restore your original dotfiles by using `./restore.sh $RESTOREDATE` where `$RESTOREDATE` is the date folder name you want to restore.

> The restore script does not currently restore system settings--only your original dotfiles. To restore system settings, you'll need to manually undo what you don't like (so don't forget to fork, review, tweak)

# 3.x.x+ Upgrade Instructions!

`3.0.0` brings huge changes. If you have made any modifications (and didn't make your own fork), you will want to backup your dotfiles prior to running `git-up` or `git pull` on `~/.dotfiles`.

Do the following to upgrade your ~/.dotfiles safely:

1. backup your dotfiles: `cp -R ~/.dotfiles ~/.dotfiles_old`
2. `cd ~/.dotfiles`
3. update dotfiles: `git up` or `git pull`
4. remove old submodule location: `rm -rf .vim` (now lives in `homedir/.vim`)
5. inspect `install.sh` and the `software/` directory to make sure all the software you want is installed
6. inspect `homedir/*` for any changes you want to port from `./dotfiles_old`
7. run `install.sh` again

# Additional

## VIM as IDE

I am moving away from using `Atom` and instead using `vim` as my IDE. I use Vundle to manage vim plugins (instead of pathogen). Vundle is better in many ways and is compatible with pathogen plugins. Additionally, vundle will manage and install its own plugins so we don't have to use git submodules for all of them.

## Crontab

You can `cron ~/.crontab` if you want to add my nightly cron software updates.

> \\[0_0]/ - Note that this may wake you in the morning to compatibility issues so use only if you like being on the edge

## Remap Caps-Lock

- I highly recommend remapping your Caps Lock key to Control per [Dr. Bunsen](http://www.drbunsen.org/remapping-caps-lock/):
  ![Remap Caps Lock](https://raw.githubusercontent.com/atomantic/dotfiles/master/img/remap_capslock.png)

# Settings

This project changes a number of settings and configures software on MacOS.
Here is the current list:

## Prompt Driven Configuration

The following will only happen if you agree on the prompt

- install a gitshots script to take a photo using your camera on every git commit (these go in as a post-commit hook to your .git_template)
 - overwrite your /etc/hosts file using the [StevenBlack/hosts](https://github.com/StevenBlack/hosts) project. Add custom entries in `./configs/hosts.local`.

## Security

- Enable firewall
- Enable firewall stealth mode (no response to ICMP / ping requests)
- Disable remote apple events
- Disable wake-on modem
- Disable wake-on LAN
- Disable guest account login

## General System Changes

- always boot in verbose mode (not MacOS GUI mode)
- Disable the sound effects on boot
- Expand save panel by default
- Expand print panel by default
- allow 'locate' command
- Save to disk (not to iCloud) by default
- Automatically quit printer app once the print jobs complete
- Display ASCII control characters using caret notation in standard text views
- Disable the crash reporter
- Reveal IP, hostname, OS, etc. when clicking clock in login window
- Disable smart quotes as they’re annoying when typing code
- Disable smart dashes as they’re annoying when typing code

## Finder Configs
- Allow quitting via ⌘ + Q; doing so will also hide desktop icons
- Show hidden files by default
- Show all filename extensions
- Show status bar
- Show path bar
- Allow text selection in Quick Look
- Display full POSIX path as Finder window title
- When performing a search, search the current folder by default
- Disable the warning when changing a file extension
- Enable spring loading for directories
- Remove the spring loading delay for directories
- Avoid creating .DS_Store files on network volumes
- Disable disk image verification
- Automatically open a new Finder window when a volume is mounted
- Use list view in all Finder windows by default
- Disable the warning before emptying the Trash
- Empty Trash securely by default
- Enable AirDrop over Ethernet and on unsupported Macs running Lion
- Show the ~/Library folder
- Expand the following File Info panes: “General”, “Open with”, and “Sharing & Permissions”

## Dock & Dashboard

- Enable highlight hover effect for the grid view of a stack (Dock)
- Set the icon size of Dock items to 36 pixels
- Change minimize/maximize window effect to scale
- Minimize windows into their application’s icon
- Enable spring loading for all Dock items
- Show indicator lights for open applications in the Dock
- Don’t animate opening applications from the Dock
- Speed up Mission Control animations
- Don’t automatically rearrange Spaces based on most recent use
- Remove the auto-hiding Dock delay
- Remove the animation when hiding/showing the Dock
- Automatically hide and show the Dock
- Make Dock icons of hidden applications translucent
- Make Dock more transparent

## Configuring Hot Corners

- Top left screen corner → Mission Control
- Top right screen corner → Desktop
- Bottom right screen corner → Start screen saver

## Spotlight

- Disable Spotlight indexing for any volume that gets mounted and has not yet been indexed
- Change indexing order and disable some file types from being indexed
- Load new settings before rebuilding the index
- Make sure indexing is enabled for the main volume

## iTerm2

- Installing the Solarized Dark theme for iTerm
- Don’t display the annoying prompt when quitting iTerm
- Hide tab title bars
- Set system-wide hotkey to show/hide iterm with ctrl+tick ( `^` + `)
- Set normal font to Hack 12pt
- Set non-ascii font to Roboto Mono for Powerline 12pt

## Time Machine

- Prevent Time Machine from prompting to use new hard drives as backup volume
- Disable local Time Machine backups

## Activity Monitor

- Show the main window when launching Activity Monitor
- Visualize CPU usage in the Activity Monitor Dock icon
- Show all processes in Activity Monitor
- Sort Activity Monitor results by CPU usage

## Dashboard, iCal, TextEdit, and Disk Utility

- Enable Dashboard dev mode (allows keeping widgets on the desktop)
- Use plain text mode for new TextEdit documents
- Open and save files as UTF-8 in TextEdit
- Enable the debug menu in Disk Utility

## Mac App Store

- Enable the WebKit Developer Tools in the Mac App Store
- Enable Debug Menu in the Mac App Store

## Messages

- Disable automatic emoji substitution (i.e. use plain text smileys)
- Disable smart quotes as it’s annoying for messages that contain code


# Software Installation

homebrew, fontconfig, git, nvm (node + npm), and zsh (latest) are all installed inside the `install.sh` as foundational software for running this project.

Additional software is configured in separate files within the `software/` directory and can be customized in your own fork/branch:

- `software/brew.js` - Homebrew utilities and command-line tools
- `software/cask.js` - Homebrew desktop applications (GUI apps)
- `software/npm.js` - Global NPM packages
- `software/mas.js` - Mac App Store applications
- `software/gem.js` - Ruby gems

## .BrewFile Support

You can add additional Homebrew packages by creating a `.BrewFile` in the `homedir/` directory. This file should contain one package per line, with comments starting with `#`.

Example `.BrewFile`:
```
# Additional Homebrew packages
htop
neofetch
ripgrep
fd
exa
```

## Installation Prompts

When running the installation script, you'll be prompted for each software type:

1. **Homebrew utilities** - Command-line tools and utilities
2. **Homebrew desktop apps** - GUI applications
3. **NPM global packages** - Node.js packages installed globally
4. **Mac App Store apps** - Applications from the Mac App Store
5. **Ruby gems** - Ruby packages

Each prompt allows you to choose whether to install that category of software or skip it.

# License

This project is licensed under ISC. Please fork, contribute and share.

# Contributions

Contributions are always welcome in the form of pull requests with explanatory comments.

Please refer to the [Contributor Covenant](CODE_OF_CONDUCT.md)

# Loathing, Mehs and Praise

1. Loathing should be directed into pull requests that make it better. woot.
2. Bugs with the setup should be put as GitHub issues.
3. Mehs should be > /dev/null
4. Praise should be directed to [![@antic](https://img.shields.io/twitter/follow/antic.svg?style=social&label=@antic)](https://twitter.com/antic)

# Author

More here: https://adameivy.com

# ¯\\_(ツ)_/¯ Warning / Liability

> Warning:
> The creator of this repo is not responsible if your machine ends up in a state you are not happy with. If you are concerned, look at the code to review everything this will do to your machine :)
