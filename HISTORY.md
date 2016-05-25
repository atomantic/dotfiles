<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [Release History](#release-history)
    - [<sup>v2.1.0</sup>](#supv210sup)
    - [<sup>v2.0.0</sup>](#supv200sup)
    - [<sup>v1.1.0</sup>](#supv110sup)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# Release History

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
