# Path to your oh-my-zsh configuration.
export ZSH=$HOME/.dotfiles/oh-my-zsh
# if you want to use this, change your non-ascii font to Droid Sans Mono for Awesome
export ZSH_THEME="spaceship"
 SPACESHIP_PROMPT_ORDER=(
time          # Time stampts section
user          # Username section
dir           # Current directory section
host          # Hostname section
git           # Git section (git_branch + git_status)
package       # Package version
node          # Node.js section
venv          # virtualenv section
pyenv         # Pyenv section
exec_time     # Execution time
line_sep      # Line break
battery       # Battery level and status
vi_mode       # Vi-mode indicator
jobs          # Background jobs indicator
exit_code     # Exit code section
char          # Prompt character
  )
# Set to this to use case-sensitive completion
export CASE_SENSITIVE="true"

# disable weekly auto-update checks
# export DISABLE_AUTO_UPDATE="true"

# disable colors in ls
# export DISABLE_LS_COLORS="true"

# disable autosetting terminal title.
export DISABLE_AUTO_TITLE="true"

# Which plugins would you like to load? (plugins can be found in ~/.dotfiles/oh-my-zsh/plugins/*)
# Example format: plugins=(rails git textmate ruby lighthouse)
plugins=(colorize compleat dirpersist autojump git history cp brew brew-cask colored-man-pages common-aliases  extract fasd  gitfast git-extras git-flow github git_remote_branch mkcd jsontools last-working-dir httpie node npm osx ruby fancy-ctrl-z safe-paste zsh-autosuggestions zsh-navigation-tool zsh_reload nvm-auto)

source $ZSH/oh-my-zsh.sh

test -e "${HOME}/.iterm2_shell_integration.zsh" && source "${HOME}/.iterm2_shell_integration.zsh"

source /usr/local/opt/nvm/nvm.sh

autoload -U add-zsh-hook
load-nvmrc() {
  if [[ -f .nvmrc && -r .nvmrc ]]; then
    nvm use &> /dev/null
  elif [[ $(nvm version) != $(nvm version default)  ]]; then
    nvm use default &> /dev/null
  fi
}
add-zsh-hook chpwd load-nvmrc
load-nvmrc

# Customize to your needs...
unsetopt correct

# run fortune on new terminal :)
# fortune
