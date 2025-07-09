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

## Neovim Setup Notes

- Uses LazyVim for extensible, high-performance setup.
- Statusline: lualine.nvim, themed and sectioned to reflect p10k/powerlevel10k shell info for visual harmony between shell and Neovim.
- Code navigation: navbuddy is primary (LSP-powered, interactive, superior to outline.nvim); outline.nvim not used/redundant.
- Remote editing:
  - Native: Neovim 0.10+ supports scp:// and sftp:// URLs directly in :edit and file explorers (e.g., oil.nvim, nvim-tree).
  - Advanced: Consider distant.nvim for interactive, persistent remote sessions when required.
- Treesitter, LSP, Copilot, Telescope configured for all major languages (Python, JS/TS, C#, Kotlin, etc.) and workflows (incl. GCP, Android, Unity).
- All configs for appearance and key plugins (statusline, tree, navigation) should preserve consistency with shell prompt for clarity.

(Expand this section with future major Neovim decisions or rules).
