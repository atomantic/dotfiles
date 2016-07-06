<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [Release History](#release-history)
    - [<sup>v3.1.0</sup>](#supv301sup)
    - [<sup>v3.0.0</sup>](#supv300sup)
    - [<sup>v2.1.0</sup>](#supv210sup)
    - [<sup>v2.0.0</sup>](#supv200sup)
    - [<sup>v1.1.0</sup>](#supv110sup)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# Release History

### <sup>v3.1.0</sup>
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
```cd ~/.vim/bundle/YouCompleteMe
./install.py --all
 ```
 * now more `bower` (just use npm)
 * fixed OSX clipboard copy (yank in vim now copies to OSX clipboard)
 * adding `npm config set save-exact true` at install time
  * ensures we always pin node modules
  * keeps consistent dev/build environments
  * saves from security and feature problems when people abuse SemVer

### <sup>v3.0.0</sup>
 * BREAKING CHANGES!!!
 * now using node.js to run install list from `config.js`
 * moved dotfile sources into `homedir/*`
 * removed `osx.sh` script (all software/config exists now in `install.sh` and `config.js`)
 * remove Google Chrome install and configuration (chrome no longer likes to be installed via homebrew)
 * remove IOS simulator in launchpad config
 * backups are now created every time you run `./install.sh` and stored in datetime subfolders (in `./dotfiles_backup`)

### <sup>v2.1.0</sup>
 * Now using powerlevel9k theme with awesome-patched fonts

### <sup>v2.0.0</sup>
 * switched to using vim as primary editor/IDE
 * vim plugings now use vundle instead of pathogen

### <sup>v1.1.0</sup>
 * Added dotfiles backup and restore.sh script
     * you can now restore your previous dotfiles with ./restore.sh
 * prompting the user more (e.g. `brew upgrade` is now optional)
