---
title: fix: Resolve installer paths from checkout directory
type: fix
status: completed
date: 2026-05-14
---

# fix: Resolve installer paths from checkout directory

## Summary

Make the installer work from the actual checkout path instead of assuming the repository lives at `$HOME/.dotfiles`. The fix should derive the repository root from the script location, use that path for helper sourcing, stow, nested package installation, and Neovim symlinking, and keep package installation from retrying broken commands indefinitely.

## Assumptions

*This plan was authored without synchronous user confirmation. The items below are agent inferences that fill gaps in the input -- un-validated bets that should be reviewed before implementation proceeds.*

- The installer should remain usable from any clone location, including `dotfiles-marc`, while preserving the existing `homedir` stow layout.
- Documentation may still mention cloning to `~/.dotfiles`; the active fix is scoped to executable installer behavior unless stale docs directly block verification.

## Requirements

- R1. `install.sh` must not require the checkout to be located at `$HOME/.dotfiles`.
- R2. Nested installer scripts must resolve helper files and data files relative to their own script directory.
- R3. Stow and Neovim symlink setup must point at the actual checkout directory.
- R4. Package installation failures must stop the install instead of printing `[ok]` and continuing through repeated failing installs.

## Scope Boundaries

- Do not redesign the full macOS provisioning flow.
- Do not replace GNU Stow or change the `homedir` package structure.
- Do not migrate package definitions out of `packages.json`.

## Context & Research

### Relevant Code and Patterns

- `install.sh` is the main entry point and currently owns helper sourcing, stow invocation, Neovim symlink setup, and `install_packages.sh` handoff.
- `install_packages.sh` reads `packages.json` and delegates package installation to helpers in `lib_sh/requirers.sh`.
- `AGENTS.md` requires shell scripts to use `#!/usr/bin/env bash`, source helper libraries near the top, and keep installs idempotent.

### Institutional Learnings

- No `docs/solutions/` directory exists in this repository, so there are no local learning documents to carry forward.

### External References

- None needed; this is a local shell-path and idempotence fix.

## Key Technical Decisions

- Derive `DOTFILES_DIR` with `BASH_SOURCE[0]` in executable scripts: this is stable when the user runs scripts from another working directory and avoids hard-coding a clone name.
- Keep package option metadata out of `brew install` arguments: strings like `restart_service: :changed` and absent options represented as `null` are package metadata, not Homebrew CLI operands.
- Return non-zero from failed package helpers: continuing after a failed install hides the real error and creates the observed repeated install-failure loop.

## Implementation Units

### U1. Resolve Installer Root Dynamically

**Goal:** Make top-level installer paths derive from the current checkout.

**Requirements:** R1, R3

**Dependencies:** None

**Files:**
- Modify: `install.sh`

**Approach:**
- Define a script-root variable near the top of `install.sh`.
- `cd` into that directory once so existing relative file references keep working.
- Use the script-root variable for helper sourcing, GNU Stow `--dir`, `install_packages.sh` invocation, and Neovim symlink targets.

**Patterns to follow:**
- Existing shell helper sourcing in `install.sh`.
- Existing stow package layout under `homedir`.

**Test scenarios:**
- Happy path: from the repo root, `bash -n install.sh` succeeds.
- Integration: when the checkout path is not `$HOME/.dotfiles`, the stow command uses the checkout directory and no longer errors that `$HOME/.dotfiles` is invalid.
- Edge case: if `~/.config/nvim` already exists as a non-symlink, the installer should warn and avoid overwriting it.

**Verification:**
- `install.sh` contains no active stow or symlink dependency on `$HOME/.dotfiles`.
- Shell syntax validation passes.

### U2. Resolve Package Installer Root Dynamically

**Goal:** Allow `install_packages.sh` to run from any working directory.

**Requirements:** R2

**Dependencies:** None

**Files:**
- Modify: `install_packages.sh`

**Approach:**
- Define the same script-root variable pattern as `install.sh`.
- `cd` into the repo root before reading `packages.json`.
- Source helper libraries through the derived path.

**Patterns to follow:**
- `install.sh` script-root pattern from U1.

**Test scenarios:**
- Happy path: `bash -n install_packages.sh` succeeds.
- Integration: package JSON and helper sourcing are repo-relative, not caller-CWD-relative.

**Verification:**
- The script can be invoked by path without relying on the caller already being in the repo root.

### U3. Stop Repeated Broken Package Installs

**Goal:** Prevent package installation from looping through repeated invalid `brew install ... null` commands.

**Requirements:** R4

**Dependencies:** U2

**Files:**
- Modify: `install_packages.sh`
- Modify: `lib_sh/requirers.sh`

**Approach:**
- Convert missing `.options` values from JSON to an empty string instead of literal `null`.
- Pass options to helpers only when present.
- Make package helper failures return non-zero and make the package installer exit on those failures.
- Keep Brewfile-like metadata handling local to the brew helper where possible.

**Patterns to follow:**
- Existing `require_brew`, `require_cask`, `require_npm`, `require_gem`, `require_mas`, and `require_vscode` helper API.

**Test scenarios:**
- Happy path: packages without `options` call helpers with only the package name.
- Error path: a failed package install returns non-zero and stops `install_packages.sh`.
- Integration: packages with `link: false` or `restart_service: :changed` metadata do not pass those strings as package names.

**Verification:**
- No generated command includes a literal `null` package argument.
- Shell syntax validation passes for modified scripts.

## Open Questions

### Resolved During Planning

- Should docs that recommend cloning to `~/.dotfiles` be changed in this fix? No. The executable path bug is independent, and documentation clone-path preference can remain unless it causes runtime behavior.

### Deferred to Implementation

- Whether the current machine has all packages already installed is execution-time state; the fix should be validated through syntax checks and targeted installer observation rather than assuming a clean macOS host.

## Sources & References

- Related code: `install.sh`
- Related code: `install_packages.sh`
- Related code: `lib_sh/requirers.sh`
- Project guidance: `AGENTS.md`
