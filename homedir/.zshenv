fpath=($fpath $HOME/.zsh/func)
typeset -U fpath

# uv
export PATH="$HOME/.local/bin:$PATH"
if [ -d "$HOME/.cargo" ]; then
    . "$HOME/.cargo/env";
fi
