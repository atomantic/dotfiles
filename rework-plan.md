# Dotfiles Rework Plan

## Value

The dotfiles should represent the current productive setup while staying usable
across two recurring environments:

- Software engineering machines: private or work macOS laptops and desktops.
- AI nodes: Mac mini or Mac Studio machines on macOS, plus remote Linux
  containers across different distributions.

The goal is to simplify the repository and make it more generally reusable,
taking inspiration from <https://github.com/omerxx/dotfiles/>. Nix is worth
evaluating as a real option this time because the current setup spans too many
dependency managers: `asdf`, `pyenv`, `nvm`, `mise`, curl installers, Homebrew,
gems, npm globals, pip globals, and MAS.

## Inventory

1. Review all Brew, gem, npm global, pip global, and MAS packages.
2. Decide which packages are still actively needed.
3. Identify packages that can be replaced by more autonomous open-source
   alternatives, such as Docker to Podman or local Kubernetes alternatives.
4. Identify services that should move out of the workstation setup and into the
   NAS container manager through Docker Compose.

## Config Layout

- Move tools that support `~/.config` into the repository `config/` folder.
- Keep app folders such as `nvim` and `tmux` linkable as individual config targets.
- Evaluate GNU Stow, `tuckr`, or a similar tool for shared config plus
  platform-specific config such as `config/` and `config_macos/`.

## Shell

Oh My Zsh is useful but heavy and can create friction with Git-related shell
behavior. Evaluate whether a lighter modern shell setup can replace or slim the
current OMZ footprint.

## Packages

Prefer Brewfiles where possible because they support one-shot installation and
reduce custom loop failure modes.

For packages that are not available through Homebrew, keep a structured manifest
like `packages.json`, but consider renaming it to avoid confusion with npm
package metadata.

## Coding

Slim the primary coding surface area:

- Zed
- Neovim
- Environment-specific JetBrains IDEs when valuable, such as Rider for C# or
  DataSpell for SQL and data workflows.
