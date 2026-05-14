#!/usr/bin/env bash

# This script contains the function for setting up asdf plugins

function install_asdf_plugins() {
  bot "Installing ASDF plugins..."
  if ! command -v asdf >/dev/null 2>&1; then
    local asdf_script
    asdf_script="$(brew --prefix asdf 2>/dev/null)/libexec/asdf.sh"
    if [[ -r "$asdf_script" ]]; then
      source "$asdf_script"
    fi
  fi

  if ! command -v asdf >/dev/null 2>&1; then
    warn "asdf is not available; skipping ASDF plugin setup"
    return 0
  fi

  for plugin in nodejs bun; do
    if ! asdf plugin list | grep -qx "$plugin"; then
      asdf plugin add "$plugin" || return 1
    fi
  done
  ok
}
