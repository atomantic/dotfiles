# iTerm2 Project Tabs — Setup Guide

The **emoji + project-named iTerm2 tab** feature: each git project gets a deterministic emoji and
tab color, auto-applied when you `cd` into it.

**In this repo, the feature already ships in [`homedir/.shellfn`](../homedir/.shellfn)** and is
installed on any machine by the normal dotfiles installer (`install.sh` symlinks `homedir/.shellfn`
to `~/.shellfn`, which is sourced from `~/.profile`). If you're setting up a machine with these
dotfiles, you get it automatically — nothing else to do.

The rest of this doc is for **porting the feature to a machine that does _not_ use these dotfiles**:
it explains what the feature does and includes the self-contained code block to paste in.

## What this feature does

- Each git project gets a **deterministic emoji + tab color**, derived from a hash of the
  project's directory name. The same project always looks the same across sessions and machines.
- Tabs are **auto-labeled on `cd`**: when you enter any directory inside a git repo, the tab
  title becomes `<emoji> <project-name>` and the tab background color changes to match.
- Leaving all git repos resets the tab to defaults.
- A manual command `iproj` is also provided, plus `iproj-list` to preview identities.

Example tab titles: `🔷 PortOS`, `🦊 slashdo`, `⚡ my-api`.

## Requirements

| Requirement | Why | Check |
|---|---|---|
| **zsh** | Uses `add-zsh-hook`, `chpwd`, `precmd` | `echo $ZSH_VERSION` |
| **iTerm2** | OSC escape codes `\e]6;1;bg;...` for tab color are iTerm2-specific | `echo $TERM_PROGRAM` → `iTerm.app` |
| **macOS `md5`** | Hash function for deterministic emoji/color. **Note:** Linux has `md5sum`, not `md5` | `command -v md5` |

> On a non-macOS machine, replace the `md5` call in `_iterm2_hash` with
> `md5sum | awk '{print $1}'` (see the porting note at the bottom).

The tab-color escape sequences (`\e]6;1;bg;...`) only work in **iTerm2**. Titles (`\e]1;`/`\e]2;`)
work in most terminals, but the color feature will silently no-op elsewhere (guarded by the
`[[ "$TERM_PROGRAM" == "iTerm.app" ]]` checks).

## Installation (non-dotfiles machine)

> If the target machine uses these dotfiles, skip this — `install.sh` already sets it up.

1. Pick where to put the code. In these dotfiles it lives in `homedir/.shellfn` (symlinked to
   `~/.shellfn`, sourced from `~/.profile`). On a machine without that file, just paste the block
   below directly into `~/.zshrc` (or any file you source from it).

2. Add the following block **verbatim**:

```zsh
#########################################
# iTerm2 tab labeling
# Deterministic emoji + color per project name via hash.
# Same project always gets the same visual identity across sessions.
#
# Usage: iproj [path]
#   iproj .              # label current tab for this project
#   iproj ~/projects/foo # label for a specific project
#   iproj                # reset tab to defaults

# 60 visually distinct emojis — indexed by hash of project name
_ITERM2_EMOJI_POOL=(
  "🔷" "🟢" "🟣" "🟠" "🔴" "🩵" "💎" "🌀" "⚡" "🔥"
  "🌊" "🍀" "🎯" "🚀" "💜" "🧊" "🌙" "☀️" "🎲" "🏔️"
  "🐙" "🦊" "🐺" "🦅" "🐝" "🪐" "🌵" "🍊" "🫐" "🍋"
  "⭐" "🔮" "🎪" "🏗️" "⚓" "🛡️" "🎸" "🎭" "🧬" "🔬"
  "💡" "🗿" "🌋" "❄️" "🌈" "🎨" "🧲" "⚙️" "🔑" "🪨"
  "🦎" "🐋" "🦜" "🌸" "🍄" "🌿" "🪸" "🐚" "🦋" "🪻"
)

# 30 distinct tab colors — hue-spread RGB values
_ITERM2_COLOR_POOL=(
  "40 100 200"   # blue
  "50 160 70"    # green
  "140 60 180"   # purple
  "210 130 40"   # orange
  "190 55 55"    # red
  "50 160 180"   # cyan
  "180 60 120"   # magenta
  "100 140 50"   # olive
  "60 120 160"   # steel blue
  "170 100 60"   # brown
  "80 60 160"    # indigo
  "50 180 120"   # teal
  "200 80 80"    # coral
  "120 100 180"  # lavender
  "160 150 40"   # gold
  "60 140 140"   # dark cyan
  "180 80 160"   # orchid
  "100 160 100"  # sage
  "70 80 180"    # royal blue
  "180 120 80"   # tan
  "100 50 130"   # deep purple
  "40 170 100"   # emerald
  "200 100 120"  # rose
  "80 130 60"    # forest
  "150 80 40"    # rust
  "60 100 130"   # slate
  "170 60 90"    # crimson
  "90 170 60"    # lime
  "130 70 150"   # violet
  "50 130 90"    # jade
)

function _iterm2_hash() {
  # Deterministic hash of a string -> integer
  # (macOS `md5`. On Linux use: md5sum | awk '{print $1}' | tr -d 'a-f' | cut -c1-8)
  printf '%s' "$1" | md5 | tr -d 'a-f' | cut -c1-8
}

function _iterm2_project_emoji() {
  local hash=$(_iterm2_hash "$1")
  local idx=$(( (hash % ${#_ITERM2_EMOJI_POOL[@]}) + 1 ))
  echo "${_ITERM2_EMOJI_POOL[$idx]}"
}

function _iterm2_project_color() {
  # Use a different slice of the hash for color than emoji
  local hash=$(_iterm2_hash "color_$1")
  local idx=$(( (hash % ${#_ITERM2_COLOR_POOL[@]}) + 1 ))
  echo "${_ITERM2_COLOR_POOL[$idx]}"
}

function _iterm2_tab_color() {
  local r="$1" g="$2" b="$3"
  printf '\e]6;1;bg;red;brightness;%d\a' "$r"
  printf '\e]6;1;bg;green;brightness;%d\a' "$g"
  printf '\e]6;1;bg;blue;brightness;%d\a' "$b"
}

function _iterm2_tab_color_reset() {
  printf '\e]6;1;bg;*;default\a'
}

function _iterm2_tab_title() {
  # \e]1; sets tab title, \e]2; sets window title
  # Set both to prevent iTerm2 shell integration from overriding
  printf '\e]1;%s\a' "$1"
  printf '\e]2;%s\a' "$1"
}

function _iterm2_label_tab() {
  local project_name="$1"
  local emoji="$(_iterm2_project_emoji "$project_name")"
  local color_str="$(_iterm2_project_color "$project_name")"
  local -a rgb=( ${=color_str} )
  _iterm2_tab_color "${rgb[1]}" "${rgb[2]}" "${rgb[3]}"
  _iterm2_tab_title "${emoji} ${project_name}"
  echo "${emoji} ${project_name}"
}

function iproj() {
  # Reset mode — no args
  if [[ $# -eq 0 ]]; then
    _iterm2_tab_color_reset
    _iterm2_tab_title ""
    ITERM2_PROJECT_ACTIVE=""
    echo "Tab reset to defaults"
    return 0
  fi

  local project_path="$1"

  if [[ "$project_path" == "." ]]; then
    project_path="$(pwd)"
  else
    project_path="$(cd "$project_path" 2>/dev/null && pwd)"
  fi

  if [[ -z "$project_path" || ! -d "$project_path" ]]; then
    echo "Error: Directory not found: $1"
    return 1
  fi

  local project_name=$(basename "$project_path")
  ITERM2_PROJECT_ACTIVE="$project_path"
  local label="$(_iterm2_label_tab "$project_name")"
  echo "iTerm2 tab: $label"
}

# Preview all project identities
# Usage: iproj-list [directory]  (defaults to cwd)
function iproj-list() {
  local search_dir="${1:-$PWD}"
  echo "Project tab identities in $search_dir:"
  for repo in "$search_dir"/*/; do
    [[ -d "$repo/.git" ]] || continue
    local name=$(basename "$repo")
    local emoji="$(_iterm2_project_emoji "$name")"
    local color_str="$(_iterm2_project_color "$name")"
    local -a rgb=( ${=color_str} )
    printf "  %s %-25s  rgb(%3d,%3d,%3d)\n" "$emoji" "$name" "${rgb[1]}" "${rgb[2]}" "${rgb[3]}"
  done
}

# Auto-apply iTerm2 tab labels on cd — finds nearest git root
ITERM2_PROJECT_ACTIVE=""

function _iterm2_apply_on_cd() {
  [[ "$TERM_PROGRAM" == "iTerm.app" ]] || return 0

  local git_root=""
  local search_dir="$PWD"

  # Walk up to find nearest .git directory
  while [[ "$search_dir" != "/" ]]; do
    if [[ -d "$search_dir/.git" ]]; then
      git_root="$search_dir"
      break
    fi
    search_dir="$(dirname "$search_dir")"
  done

  if [[ "$git_root" == "$ITERM2_PROJECT_ACTIVE" ]]; then
    return 0
  fi

  if [[ -n "$git_root" ]]; then
    ITERM2_PROJECT_ACTIVE="$git_root"
    local project_name="$(basename "$git_root")"
    _iterm2_label_tab "$project_name" > /dev/null
  elif [[ -n "$ITERM2_PROJECT_ACTIVE" ]]; then
    ITERM2_PROJECT_ACTIVE=""
    _iterm2_tab_color_reset
    _iterm2_tab_title ""
  fi
}

autoload -U add-zsh-hook
add-zsh-hook chpwd _iterm2_apply_on_cd
# Apply on every prompt until it sticks, then switch to a lighter keep-alive
# iTerm2 shell integration can override tab titles, so we re-apply on precmd
function _iterm2_precmd_label() {
  [[ "$TERM_PROGRAM" == "iTerm.app" ]] || return 0
  [[ -n "$ITERM2_PROJECT_ACTIVE" ]] || _iterm2_apply_on_cd
  # Re-apply title on each prompt to prevent iTerm2 shell integration from overriding
  if [[ -n "$ITERM2_PROJECT_ACTIVE" ]]; then
    local project_name="$(basename "$ITERM2_PROJECT_ACTIVE")"
    local emoji="$(_iterm2_project_emoji "$project_name")"
    printf '\e]1;%s\a' "${emoji} ${project_name}"
    printf '\e]2;%s\a' "${emoji} ${project_name}"
  fi
}
add-zsh-hook precmd _iterm2_precmd_label
```

3. Reload the shell: `source ~/.zshrc` (or open a fresh iTerm2 tab).

## How it works

- **`_iterm2_hash`** — hashes the project name with `md5`, strips hex letters, takes the first
  8 digits → a stable integer. Deterministic, so identity is consistent everywhere.
- **`_iterm2_project_emoji` / `_iterm2_project_color`** — index into the emoji/color pools with
  `hash % pool_size`. Color hashes a `color_`-prefixed string so emoji and color pick
  independently.
- **Tab color** uses iTerm2 OSC `\e]6;1;bg;<channel>;brightness;<0-255>\a`.
- **Tab/window title** uses OSC `\e]1;` (tab) and `\e]2;` (window). Both are set because iTerm2
  shell integration otherwise overwrites the title.
- **Auto-labeling** — a `chpwd` hook walks up from `$PWD` to the nearest `.git` directory and
  labels the tab with that repo's name. A `precmd` hook re-applies the title every prompt so the
  shell-integration override doesn't win. `ITERM2_PROJECT_ACTIVE` caches the current repo root to
  avoid redundant re-labeling.

## Verification

Run these on the new machine after installing:

```zsh
echo $TERM_PROGRAM        # must print: iTerm.app
command -v md5            # must resolve (macOS)
iproj-list ~/projects     # prints emoji + color for each git repo under ~/projects
cd ~/some/git/project     # tab should change to "<emoji> project-name" + colored
iproj                     # resets the tab
```

If titles change but colors don't, confirm you're in **iTerm2** (not Terminal.app, Ghostty, etc.)
— the color escape codes are iTerm2-only.

## Porting to Linux / non-macOS (optional)

The only OS-specific piece is the hash. Change `_iterm2_hash` to:

```zsh
function _iterm2_hash() {
  printf '%s' "$1" | md5sum | awk '{print $1}' | tr -d 'a-f' | cut -c1-8
}
```

Everything else is portable **as long as the terminal is iTerm2** (which is macOS-only, so in
practice this feature is macOS + iTerm2). On other terminals the titles will still work but tab
colors won't.
