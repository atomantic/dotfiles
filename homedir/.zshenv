fpath=($fpath $HOME/.zsh/func)
typeset -U fpath

if [ -d "$HOME/.local/bin" ]; then
  # configure uv
  export PATH="$HOME/.local/bin:$PATH"
fi

if [ -d "$HOME/.cargo" ]; then
  # configure cargo
  . "$HOME/.cargo/env";
fi
