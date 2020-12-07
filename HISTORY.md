<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [Release History](#release-history)  
  - [v5.1.0](#v510)
  - [v5.0.0](#v500)
  - [v4.4.2](#v442)
  - [v4.4.1](#v441)
  - [v4.4.0](#v440)
  - [v4.3.0](#v430)
  - [v4.2.0](#v420)
  - [v4.1.1](#v411)
  - [v4.1.0](#v410)
  - [v4.0.0](#v400)
  - [v3.3.2](#v332)
  - [v3.3.1](#v331)
  - [v3.3.0](#v330)
  - [v3.2.1](#v321)
  - [v3.2.0](#v320)
  - [v3.1.0](#v310)
  - [v3.0.0](#v300)
  - [v2.1.0](#v210)
  - [v2.0.0](#v200)
  - [v1.1.0](#v110)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# Release History

## v5.1.0
 * update passwordless sudo to use method in [issue #35](https://github.com/atomantic/dotfiles/issues/35)

## v5.0.0
 * each segment of setup now requires positive user input to change the system
 * replace `NERDtree` in vim with `netrw`
 * add spell check to `vim`
 * fix package installs (run in series to prevent homebrew from stepping on itself in parallel)
 * update /etc/hosts file from someonewhocares.org

## v4.4.2
 * add .iterm2_shell_integration.zsh

## v4.4.1
 * re-enable notification center
 * fix gitshots optional ability by renaming post-commit -> gitshot-pc

## v4.4.0
 * cleanup readme and alphabetize software installation for better fork management and listing
 * disable vim YouCompleteMe Bundle (wasn't using it anyway and was causing installation problems on some machines)
 * remove yo, generator-dockerize, box-sync from software installs
 * install node stable (rather than old 4.x.x version)
 * make gitshots optional (and only install `imagesnap` and `imagemagick` if this is on)
 * fix passwordless sudo for Sierra (optional)
 * prompt to overwrite /etc/hosts with someonewhocares.org hosts file (saved in ./configs/hosts in this project)
 * fix default wallpaper in Sierra (Sierra 2)
 * remove sudden motion disable (already disabled: https://github.com/mathiasbynens/dotfiles/pull/755/files)
 * no longer setting hibernation mode to 0 (leaving default 3): https://github.com/mathiasbynens/dotfiles/commit/a3f91f67e07b6b31760b52320e0e890f93ff4e97#commitcomment-20715991

## v4.3.0
 * vim installation fix
 * Enable firewall
 * Enable firewall stealth mode (no response to ICMP / ping requests)
 * Disable remote apple events
 * Disable wake-on modem
 * Disable wake-on LAN
 * Disable file-sharing via AFP or SMB
 * Disable guest account login
 * `pushup` alias (`git-up`, followed by `git push`)

## v4.2.0
 * new shell functions: `tre` and `sri`
 * cleanup shell functions (remove unused echo helpers)

## v4.1.1
 * fix `solarized dark` again for mocha test output coloring

## v4.1.0
 * remove `to.dir` commands (never use them)
 * add aliases: `emptytrash`, `ips`, `iplocal`, `ip`, `ifactive`, `spoton`, `spotoff`, `afk`, `reload`, `path`

## v4.0.0
 * OSX references to MacOS (includes Sierra)
 * remove aliases to things that are no longer useful (`usenode`, `useio`)
 * adding `bpc` alias for a `buzzphrase` git commit + push (don't use this on shared code)
 * `update` alias for updating all software
 * comment out GO path in `.shellpaths` (was causing slowness on machines with no go path)
 * comment out all tool shellpaths (enable them at will)
 * adding iTerm2 shell integration source
 * `weather $city` shellfn
 * cask install `little-snitch` and `micro-snitch`

## v3.3.2
 * `curltime` shell function
 * Mac Sierra fixes for key repeat
 * Mac Sierra wallpaper

## v3.3.1
 * fix restore

## v3.3.0
 * include [httpie](https://github.com/jkbrzt/httpie)

## v3.2.1
 * improved handling of `.nvmrc` files on dir change

## v3.2.0
 * adjust solarized theme to fix issue with rendering `mocha` test output
  * https://github.com/mochajs/mocha/issues/802
  * now `008` color is light grey instead of matching the solarized background color

## v3.1.0
 * new vim plugins
  * https://github.com/justinmk/vim-sneak
  * https://github.com/airblade/vim-gitgutter
  * https://github.com/tpope/vim-surround
  * https://github.com/dkprice/vim-easygrep
  * https://github.com/sjl/gundo.vim
 * vim textwidth now `120`
 * fonts now installed via brew cask (where possible)
  * thanks to @michielrensen [9db1d074](https://github.com/michielrensen/dotfiles/commit/9db1d0740eeb6df767be0f13c4706cd45c8d527f)
 * now automatically installing vim plugins
  * thanks to @michielrensen [b668fd56](https://github.com/michielrensen/dotfiles/commit/b668fd56673e12845215706cbb812f749604a3cc)
 * bubblegum theme for vim-airline
 * remove `ssh-agent` from zsh plugins (already launched by OSX)
  * thanks to @porcupie [a888494b](https://github.com/porcupie/dotfiles/commit/a888494b576dcb91fe24009dec0501504f7ffa80)
 * shorten powerline dir length to 2 dirs max
  * thanks to @Tsuki [07d4bbcd6](https://github.com/Tsuki/dotfiles/commit/07d4bbcd67dc9e961fefb318910308f424754f1d#diff-9e1651e3e42b7a9ae3b9b7492376b6cbL4)
 * adding `ag` brew
 * fix vim brew install
 * adding horizontal cursor line highlight in vim
 * killed `CTags` (not using them)
 * upgrade node to `4.4.7`
 * compile YouCompleteMe for code completion in vim:
 ```
cd ~/.vim/bundle/YouCompleteMe
./install.py --all
 ```
 * no more `bower` (just use npm)
 * fixed OSX clipboard copy (yank in vim now copies to OSX clipboard)
 * adding `npm config set save-exact true` at install time
  * ensures we always pin node modules
  * keeps consistent dev/build environments
  * saves from security and feature problems when people abuse SemVer

## v3.0.0
 * BREAKING CHANGES!!!
 * now using node.js to run install list from `config.js`
 * moved dotfile sources into `homedir/*`
 * removed `osx.sh` script (all software/config exists now in `install.sh` and `config.js`)
 * remove Google Chrome install and configuration (chrome no longer likes to be installed via homebrew)
 * remove IOS simulator in launchpad config
 * backups are now created every time you run `./install.sh` and stored in datetime subfolders (in `./dotfiles_backup`)

## v2.1.0
 * Now using powerlevel9k theme with awesome-patched fonts

## v2.0.0
 * switched to using vim as primary editor/IDE
 * vim plugings now use vundle instead of pathogen

## v1.1.0
 * Added dotfiles backup and restore.sh script
     * you can now restore your previous dotfiles with ./restore.sh
 * prompting the user more (e.g. `brew upgrade` is now optional)
