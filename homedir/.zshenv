fpath=($fpath $HOME/.zsh/func)
typeset -U fpath

if [ -d "$HOME/.local/bin" ]; then
  echo "Configure UV paths"
  # uv
  export PATH="$HOME/.local/bin:$PATH"
fi

if [ -d "$HOME/.cargo" ]; then
  echo "Configure cargo"
  . "$HOME/.cargo/env";
fi
