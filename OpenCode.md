# Dotfiles Project Reference

## Common Commands

- Install/symlink dotfiles: ./install.sh or use stow if configured
- Reload shell config: source ~/.zshrc
- Update submodules: git submodule update --init --recursive

## Directory Structure

- nvim/: Neovim configuration
- homedir/: Files to be symlinked into $HOME
- configs/: App and extra configs
- fonts/: Custom fonts (for installation)
- img/: Project images/screenshots
- lib_node/: Node.js utility scripts/libraries
- lib_sh/: Shell scripts/utilities
- oh-my-zsh/: Oh My Zsh customizations
- scripts/: Various helper or install scripts
- z-zsh/: Zsh config and plugins

## Conventions

- Never commit secrets (API keys, tokens, personal info)
- Prefer symlinks for configs over copying
- Scripts should be POSIX-compliant unless noted

## OS/Shell Notes

- Primary shell: zsh
- Some configs are MacOS-specific

---
