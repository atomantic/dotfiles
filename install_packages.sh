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

# Install common packages
for type in brew cask npm gem vscode; do
  for package in $(jq -r ".common.$type[]" packages.json); do
    require_"$type" "$package"
  done
done

for mas_package in $(jq -r '.common.mas[] | @base64' packages.json); do
    _jq() {
     echo ${mas_package} | base64 --decode | jq -r ${1}
    }
   require_mas "$(_jq '.name')" "$(_jq '.id')"
done


# Install profile-specific packages
if [[ "$PROFILE" == "private" || "$PROFILE" == "combined" ]]; then
  bot "Installing private packages..."
  for type in brew cask npm gem vscode; do
    for package in $(jq -r ".private.$type[]" packages.json); do
      require_"$type" "$package"
    done
  done
  for mas_package in $(jq -r '.private.mas[] | @base64' packages.json); do
    _jq() {
     echo ${mas_package} | base64 --decode | jq -r ${1}
    }
   require_mas "$(_jq '.name')" "$(_jq '.id')"
  done
fi

if [[ "$PROFILE" == "business" || "$PROFILE" == "combined" ]]; then
  bot "Installing business packages..."
  for type in brew cask npm gem vscode; do
    for package in $(jq -r ".business.$type[]" packages.json); do
      require_"$type" "$package"
    done
  done
  for mas_package in $(jq -r '.business.mas[] | @base64' packages.json); do
    _jq() {
     echo ${mas_package} | base64 --decode | jq -r ${1}
    }
   require_mas "$(_jq '.name')" "$(_jq '.id')"
  done
fi

ok "Package installation complete for profile: $PROFILE"
