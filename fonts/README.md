# Font Usage

This repository uses [JetBrains Mono Nerd Font](https://github.com/ryanoasis/nerd-fonts/tree/master/patched-fonts/JetBrainsMono) as the primary font for terminal, editor, and shell interfaces.

## Installation

The font is installed via Homebrew cask:

```shell
brew tap homebrew/cask-fonts
brew install --cask font-jetbrains-mono-nerd-font
```

This is automatically handled in the main `install.sh` script.

## Configuration

### Terminal (iTerm2, etc.)

After installation, select "JetBrainsMono Nerd Font" (or similar) in your terminal emulator's settings:

- iTerm2: Profiles → Text → Font

### Powerlevel10k

The Powerlevel10k theme is configured to use Nerd Font symbols via:

```shell
POWERLEVEL9K_MODE='nerdfont-complete'
```

### Neovim

No additional configuration is needed for Neovim, as it inherits the font from the terminal emulator.

## Legacy Fonts

Previous versions of this configuration used multiple fonts including:
- Powerline patched fonts
- Various Inconsolata variants
- Hack, Source Code Pro, and others

These have been consolidated to use only JetBrains Mono Nerd Font for simplicity and consistency.