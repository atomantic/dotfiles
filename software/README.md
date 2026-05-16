# Software Configuration

This directory is the canonical package source for `install.sh` and `install_packages.sh`.

## Files

- `tap.list` - Homebrew taps
- `brew.list` - Homebrew formulae
- `cask.list` - Homebrew desktop apps
- `npm.list` - Global npm packages
- `gem.list` - Ruby gems
- `mas.list` - Mac App Store apps
- `vscode.list` - VS Code extensions

Root-level files are the common package set. Profile-specific additions live in `private/` and `business/`. Profile files are appended after the common files; they do not remove or replace common entries.

## Line Format

Each list is one package per line with `#` comments allowed.

- `tap.list`: `user/repo`
- `brew.list`: `formula` or `formula|option string`
- `cask.list`: `cask`
- `npm.list`: `package`
- `gem.list`: `gem`
- `mas.list`: `Display Name|app-id`
- `vscode.list`: `extension.id`

For `brew.list`, the option string is passed through to the existing installer helper. The migrated values use the same metadata that was previously stored in `packages.json`, such as `link: false` and `restart_service: :changed`.

## Adding Packages

Edit the relevant list file and add a new line. Keep comments on their own lines when possible so the parser stays simple and the file remains easy to scan. Custom software directories must include every root manifest file listed above, even when some files are intentionally empty.
