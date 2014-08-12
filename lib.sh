#!/usr/bin/env bash

###
# some bash library helpers
# @author Adam Eivy
###

# Colors
ESC_SEQ="\x1b["
COL_RESET=$ESC_SEQ"39;49;00m"
COL_RED=$ESC_SEQ"31;01m"
COL_GREEN=$ESC_SEQ"32;01m"
COL_YELLOW=$ESC_SEQ"33;01m"
COL_BLUE=$ESC_SEQ"34;01m"
COL_MAGENTA=$ESC_SEQ"35;01m"
COL_CYAN=$ESC_SEQ"36;01m"

function ok() {
    echo -e "$COL_GREEN[ok]$COL_RESET "$1
}

function action() {
    echo -e "$COL_YELLOW[action]$COL_RESET "$1
}

function warn() {
    echo -e "$COL_YELLOW[warning]$COL_RESET "$1
}

function error() {
    echo -e "$COL_RED[error]$COL_RESET "$1
}

function require_cask() {
    echo "checking brew cask $1..."
    brew cask list $1 > /dev/null 2>&1 | true
    if [[ ${PIPESTATUS[0]} != 0 ]]; then
        action "installing $1..."
        brew cask install $1
        if [[ $? != 0 ]]; then
            error "failed to install $1! aborting..."
            exit -1
        fi
    else
        ok "$1 is installed"
    fi
}

function require_brew() {
    echo "checking brew $1..."
    brew list $1 > /dev/null 2>&1 | true
    if [[ ${PIPESTATUS[0]} != 0 ]]; then
        action "$1 installing..."
        brew install $1
        if [[ $? != 0 ]]; then
            error "failed to install $1! aborting..."
            exit -1
        fi
    else
        ok "$1 is installed"
    fi
}

function require_gem() {
    echo "checking gem install of $1..."
    if [[ $(gem list --local | grep $1 | head -1 | cut -d' ' -f1) == $1 ]];
        then
            ok "$1 is installed"
        else
            action "$1 installing..."
            gem install $1
    fi
}

function require_vagrant_plugin() {
    echo "checking vagrant plugin $1..."
    local vagrant_plugin=$1
    local vagrant_plugin_version=$2
    local grepExpect=$vagrant_plugin
    local grepStatus=$(vagrant plugin list | grep $vagrant_plugin)

    if [[ ! -z $vagrant_plugin_version ]]; then
        grepExpect=$grepExpect' ('$vagrant_plugin_version')'
    else
        # we are only looking for the name
        grepStatus=${grepStatus%% *}
    fi

    #echo 'checking if '$grepExpect' is installed via grepStatus: '$grepStatus

    if [[ $grepStatus == $grepExpect ]];
        then
            ok "$vagrant_plugin is installed"
        else
            action "missing $vagrant_plugin..."
            if [[ ! -z $vagrant_plugin_version ]]; then
                vagrant plugin install $vagrant_plugin --plugin-version $vagrant_plugin_version
            else
                vagrant plugin install $vagrant_plugin
            fi
    fi
}

function init_rbenv() {
    local rcfile=$1
    local rc=0

    if [[ ! -e $rcfile ]]; then
        touch $rcfile
    fi

    if [[ $(grep rbenv $rcfile) != 0 ]]; then
        echo 'eval "$(rbenv init -)"' >> $rcfile
    fi
}
