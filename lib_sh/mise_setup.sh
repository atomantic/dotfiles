#!/usr/bin/env bash

# This script contains the function for setting up mise tools

source ./lib_sh/echos.sh

setup_mise_tools() {
  bot "Installing mise tools (node, bun, python)..."
  if ! command -v mise >/dev/null 2>&1; then
    error "mise is not available; install mise before setting up runtimes"
    return 1
  fi

  mise install || return 1
  eval "$(mise activate bash)" || return 1
  ok
}
