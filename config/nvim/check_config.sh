#!/usr/bin/env bash

set -euo pipefail

repo_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd -P)"
tmp_root="${TMPDIR:-/tmp}"
check_home="$(mktemp -d "${tmp_root%/}/dotfiles-nvim-check.XXXXXX")"

cleanup() {
  rm -rf "$check_home"
}
trap cleanup EXIT

cd "$repo_root"

export NVIM_DOTFILES_CHECK=1
export NVIM_DOTFILES_CONFIG="$repo_root/nvim"
export XDG_CONFIG_HOME="$check_home/config"
export XDG_STATE_HOME="$check_home/state"
export XDG_CACHE_HOME="$check_home/cache"

mkdir -p "$XDG_CONFIG_HOME" "$XDG_STATE_HOME" "$XDG_CACHE_HOME"
ln -s "$repo_root/nvim" "$XDG_CONFIG_HOME/nvim"

nvim --headless \
  --cmd 'set noswapfile shadafile=NONE' \
  '+lua require("config.check_config").main()' \
  '+qa!'
