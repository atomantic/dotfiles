#!/usr/bin/env bash

set -euo pipefail

cd "$(dirname "${BASH_SOURCE[0]}")/.."

export NVIM_DOTFILES_CHECK=1
export XDG_STATE_HOME="${TMPDIR:-/tmp}/dotfiles-nvim-check/state"
export XDG_CACHE_HOME="${TMPDIR:-/tmp}/dotfiles-nvim-check/cache"

mkdir -p "$XDG_STATE_HOME" "$XDG_CACHE_HOME"

nvim --headless -u nvim/init.lua \
  --cmd 'set runtimepath^=nvim' \
  --cmd 'set noswapfile shadafile=NONE' \
  '+lua dofile("nvim/lua/config/check_config.lua").main()' \
  '+qa!'
