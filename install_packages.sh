#!/usr/bin/env bash

# This script installs packages based on the selected profile
# It reads the packages from packages.json

DOTFILES_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd -P)"
cd "$DOTFILES_DIR" || exit 1

# shellcheck disable=SC1091
source "$DOTFILES_DIR/lib_sh/echos.sh"
# shellcheck disable=SC1091
source "$DOTFILES_DIR/lib_sh/requirers.sh"

PROFILE=$1

if [[ -z "$PROFILE" ]]; then
  error "No profile selected. Please provide a profile as an argument (e.g., ./install_packages.sh combined)"
  exit 1
fi

bot "Installing packages for profile: $PROFILE"

# Install taps
for tap in $(jq -r '.taps[]' packages.json); do
  require_tap "$tap"
done

# Function to install packages for a given profile and type
install_packages() {
  local profile=$1
  local type=$2

  if [[ "$type" == "brew" || "$type" == "cask" ]]; then
    for package_json in $(jq -r ".[\"$profile\"][\"$type\"][] | @base64" packages.json); do
      _jq() {
        echo "$package_json" | base64 --decode | jq -r "$1"
      }
      local name
      local options
      name=$(_jq '.name')
      options=$(_jq '.options // ""')
      if [[ -n "$options" ]]; then
        require_"$type" "$name" "$options" || return 1
      else
        require_"$type" "$name" || return 1
      fi
    done
  elif [[ "$type" == "mas" ]]; then
    for mas_package in $(jq -r ".[\"$profile\"].mas[] | @base64" packages.json); do
      _jq() {
       echo "$mas_package" | base64 --decode | jq -r "$1"
      }
     require_mas "$(_jq '.name')" "$(_jq '.id')" || return 1
    done
  else
    for package in $(jq -r ".[\"$profile\"][\"$type\"][]" packages.json); do
      require_"$type" "$package" || return 1
    done
  fi
}

# Install common packages
bot "Installing common packages..."
for type in brew cask npm gem vscode mas; do
  install_packages "common" "$type" || exit 1
done

# Install profile-specific packages
if [[ "$PROFILE" == "private" || "$PROFILE" == "combined" ]]; then
  bot "Installing private packages..."
  for type in brew cask npm gem vscode mas; do
    install_packages "private" "$type" || exit 1
  done
fi

if [[ "$PROFILE" == "business" || "$PROFILE" == "combined" ]]; then
  bot "Installing business packages..."
  for type in brew cask npm gem vscode mas; do
    install_packages "business" "$type" || exit 1
  done
fi

ok "Package installation complete for profile: $PROFILE"
