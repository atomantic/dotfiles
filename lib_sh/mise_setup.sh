#!/usr/bin/env bash

# This script contains the function for setting up mise tools

source ./lib_sh/echos.sh

setup_mise_tools() {
  bot "Installing mise tools (node, bun, python)..."
  mise use --global node@22 bun@latest python@3.14
  ok
}
