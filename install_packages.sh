#!/usr/bin/env bash

# This script installs packages based on the selected profile
# It reads the packages from packages.json

source ./lib_sh/echos.sh
source ./lib_sh/requirers.sh

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
    for package_json in $(jq -r ".$profile.$type[] | @base64" packages.json); do
      _jq() {
        echo ${package_json} | base64 --decode | jq -r ${1}
      }
      local name=$(_jq '.name')
      local options=$(_jq '.options')
      require_"$type" "$name" "$options"
    done
  elif [[ "$type" == "mas" ]]; then
    for mas_package in $(jq -r ".$profile.mas[] | @base64" packages.json); do
      _jq() {
       echo ${mas_package} | base64 --decode | jq -r ${1}
      }
     require_mas "$(_jq '.name')" "$(_jq '.id')"
    done
  else
    for package in $(jq -r ".$profile.$type[]" packages.json); do
      require_"$type" "$package"
    done
  fi
}

# Install common packages
bot "Installing common packages..."
for type in brew cask npm gem vscode mas; do
  install_packages "common" "$type"
done

# Install profile-specific packages
if [[ "$PROFILE" == "private" || "$PROFILE" == "combined" ]]; then
  bot "Installing private packages..."
  for type in brew cask npm gem vscode mas; do
    install_packages "private" "$type"
  done
fi

if [[ "$PROFILE" == "business" || "$PROFILE" == "combined" ]]; then
  bot "Installing business packages..."
  for type in brew cask npm gem vscode mas; do
    install_packages "business" "$type"
  done
fi

ok "Package installation complete for profile: $PROFILE"
