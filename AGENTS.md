# AGENTS.md - Agentic Coding Guidelines

This document provides guidelines for AI coding agents working in this dotfiles repository.

## Project Overview

A **macOS dotfiles and system configuration automation project** that automates development environment setup including shell configuration (ZSH/Oh-My-Zsh/Powerlevel10k), editor configs (Vim, Neovim/LazyVim), Git workflows, and Homebrew package management.

## Repository Structure

```
.dotfiles/
├── install.sh             # Main installation script
├── install_packages.sh    # Software manifest installer
├── Brewfile               # Homebrew bundle (legacy/manual extras)
├── software/              # Package manifests (common/private/business)
├── homedir/               # Dotfiles symlinked to ~ via GNU stow
│   ├── .gitconfig         # Git configuration
│   ├── .zshrc             # ZSH configuration
│   ├── .shellaliases      # Shell aliases
│   ├── .shellfn           # Shell functions
│   ├── .shellvars         # Shell variables
│   └── .shellpaths        # PATH configuration
├── lib_sh/                # Shell helper libraries
│   ├── echos.sh           # Colorized output helpers
│   ├── requirers.sh       # Package requirement functions
│   └── asdf_setup.sh      # ASDF plugin setup
├── nvim/                  # Neovim/LazyVim configuration
│   └── lua/               # Lua plugin configurations
├── configs/               # App configurations (iTerm, hosts)
└── scripts/               # Utility shell scripts
```

## Build/Install Commands

```bash
./install.sh               # Full system setup (run from Terminal, not iTerm)
./install.sh ./software     # Full setup with an explicit software manifest directory
./install_packages.sh      # Install software manifests
./install_packages.sh private   # Install private overlay packages
./install_packages.sh business  # Install business overlay packages
brew bundle                 # Install Homebrew packages from Brewfile
npm install                # Install Node.js dependencies
```

### Testing
This project has no formal test suite. The `npm test` command is not implemented.

### Linting
```bash
shellcheck <script.sh>     # Lint shell scripts (installed via Homebrew)
eslint <file.js>           # Lint JavaScript files
stylua nvim/               # Format Lua files for Neovim
```

## Code Style Guidelines

### General Formatting (.editorconfig)
- **Line endings**: Unix (LF)
- **Encoding**: UTF-8
- **Indentation**: 2 spaces (no tabs)
- **Max line length**: 150 characters
- **Trailing whitespace**: Trim
- **Final newline**: Always insert

### Shell Scripts (Bash)

#### Shebang
Always use the portable shebang:
```bash
#!/usr/bin/env bash
```

#### Sourcing Libraries
Source helper libraries at the top of scripts:
```bash
source ./lib_sh/echos.sh
source ./lib_sh/requirers.sh
```

#### Output Helpers (lib_sh/echos.sh)
Use colorized output functions for user feedback:
```bash
bot "Starting installation..."    # Green robot announcement
running "Installing package"      # Yellow running indicator
ok                                # Green [ok] confirmation
action "Performing action"        # Yellow [action] header
warn "Warning message"            # Yellow [warning]
error "Error message"             # Red [error]
print_success "Success"           # ✔ checkmark
print_error "Failed"              # ✖ error mark
```

#### Package Requirements (lib_sh/requirers.sh)
Use helper functions for idempotent package installation:
```bash
require_brew package_name         # Install Homebrew formula
require_cask app_name             # Install Homebrew cask
require_npm package_name          # Install npm global package
require_gem gem_name              # Install Ruby gem
require_mas "App Name" app_id     # Install Mac App Store app
require_tap user/repo             # Add Homebrew tap
require_vscode extension_id       # Install VS Code extension
```

#### Software Manifests
- Keep package inventory in `software/*.list`
- Use `software/private/*.list` and `software/business/*.list` only for overlay packages
- Keep one package per line; use pipe-delimited metadata only where `software/README.md` documents it

#### Error Handling
- Check command exit status with `$?` or `${PIPESTATUS[0]}`
- Use descriptive error messages with the `error` function

#### User Prompts
```bash
read -r -p "Prompt message? [y|N] " response
if [[ $response =~ (yes|y|Y) ]]; then
  # Handle yes
fi
```

### JavaScript (Node.js)
- **Environment**: Node.js, ES6
- **No undefined variables** (`no-undef: 2`)
- **No unused local variables** (`no-unused-vars: 2`)
- **Quotes**: Single preferred but not enforced

### Lua (Neovim - stylua.toml)
- **Indentation**: 2 spaces
- **Line width**: 120 characters
- **Quotes**: Single (forced)
- **Call parentheses**: Always use

## Git Conventions

### Commit Messages (Conventional Commits)
Use git aliases for conventional commit types:
```bash
git feat "message"           # feat: message
git fix "message"            # fix: message
git docs "message"           # docs: message
git chore "message"          # chore: message
git refactor "message"       # refactor: message
git test "message"           # test: message
git style "message"          # style: message
git perf "message"           # perf: message
git build "message"          # build: message
git ci "message"             # ci: message
git wip "message"            # wip: message
```

With scope: `git feat -s scope "message"` → `feat(scope): message`
Breaking change: `git feat -a "message"` → `feat!: message`

### Useful Git Aliases
```bash
git s                        # Short status
git up                       # Pull with rebase and autostash
git d                        # Diff with color-words
git co branch                # Checkout
git cob branch               # Checkout -b (new branch)
git pwl                      # Push --force-with-lease (safe force push)
```

### Branch/Push Settings
- Default branch: `main`
- Pull: Rebase with autostash
- Push: Simple (current branch only)

## Naming Conventions

### Files
- Shell scripts: `snake_case.sh`
- Config files: Standard names (`.gitconfig`, `.zshrc`, etc.)
- Lua files: `snake_case.lua`

### Functions
- Shell: `snake_case` (e.g., `require_brew`, `print_success`)
- Lua: `snake_case`

### Variables
- Shell: `UPPER_CASE` for exports, `lower_case` for locals
- Colors: `COL_` prefix (e.g., `COL_GREEN`, `COL_RESET`)

## Symlink Management

Dotfiles in `homedir/` are symlinked to `$HOME` using GNU Stow:
```bash
stow -v -d "$HOME/.dotfiles" -t "$HOME" homedir
```

Backups of existing dotfiles are stored in `~/.dotfiles_backup/$(date)`.

## Important Notes

1. **Idempotent**: All scripts can be run multiple times safely
2. **Run from Terminal**: Run `install.sh` from Terminal.app, not iTerm (to preserve iTerm settings)
3. **Restore**: Use `./restore.sh $DATE` to restore from backups
4. **Submodules**: oh-my-zsh, z-zsh, and Vundle are git submodules
