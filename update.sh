#!/bin/bash
#
# update.sh — safely upgrade an existing dotfiles install.
#
# Flow (mirrors how we update other projects):
#   1. run any one-off migrations for things that changed shape since your
#      last update (these run BEFORE the pull when they need to, so the pull
#      stays conflict-free),
#   2. pull the latest code safely (stashing local work if you're off main,
#      rebasing with autostash),
#   3. re-run the interactive installer (install.sh is idempotent) so new
#      config, symlinks, and software prompts get applied.
#
# Safe to run repeatedly.

set -o pipefail

ROOT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$ROOT_DIR" || exit 1

# Colorized echo helpers (bot/ok/running/action/warn/error). Fall back to plain
# echo if the lib is missing.
if [ -f "$ROOT_DIR/lib_sh/echos.sh" ]; then
  . "$ROOT_DIR/lib_sh/echos.sh"
else
  ok()      { echo "[ok] $*"; }
  bot()     { echo "$*"; }
  running() { echo -n "$*: "; }
  action()  { echo "[action] $*"; }
  warn()    { echo "[warning] $*"; }
  error()   { echo "[error] $*"; }
fi

# Recovery state, surfaced at the end.
STASHED_BRANCH=""
BACKUP_DIR=""

bot "Dotfiles updater — I'll migrate anything that changed shape, pull the latest, and re-run the installer."

# Read a single key from an INI/git-config-style file WITHOUT expanding any
# [include]/[includeIf] directives — we want this file's own values only.
#   ini_get <file> <section> <key>
ini_get() {
  awk -v section="$2" -v key="$3" '
    /^[[:space:]]*\[/ {
      s = $0; gsub(/^[[:space:]]*\[|\][[:space:]].*$|\][[:space:]]*$/, "", s); gsub(/[[:space:]]+/, "", s)
      insec = (tolower(s) == tolower(section)); next
    }
    insec {
      line = $0; sub(/[#;].*/, "", line)
      n = index(line, "=")
      if (n > 0) {
        k = substr(line, 1, n - 1); v = substr(line, n + 1)
        gsub(/^[[:space:]]+|[[:space:]]+$/, "", k); gsub(/^[[:space:]]+|[[:space:]]+$/, "", v)
        if (tolower(k) == tolower(key)) { print v; exit }
      }
    }
  ' "$1"
}

# ###########################################################
# Migrations — one-off upgrades for things that changed shape.
# Each is a no-op when it isn't needed, so this is safe to re-run.
# ###########################################################

# #141: git identity used to be sed'd straight into the tracked
# homedir/.gitconfig, leaving every install permanently dirty and one stray
# `git add` away from committing personal info. Move it to the untracked
# ~/.gitconfig.local (pulled in via [include]) and restore the tracked file so
# the upcoming pull applies the new template cleanly.
migrate_gitconfig_identity() {
  local tracked="$ROOT_DIR/homedir/.gitconfig"
  local local_cfg="$HOME/.gitconfig.local"
  local tmp_cfg=""
  local original_local_backup=""
  local rollback_tmp=""
  local tracked_was_symlink=0

  [ -f "$tracked" ] || return 0
  [ -L "$tracked" ] && tracked_was_symlink=1

  if [ -f "$local_cfg" ] && grep -q '^\[user\]' "$local_cfg"; then
    ok "git identity already lives in ~/.gitconfig.local — nothing to migrate"
    return 0
  fi

  local name email ghuser
  name=$(ini_get "$tracked" user name)
  email=$(ini_get "$tracked" user email)
  ghuser=$(ini_get "$tracked" github user)

  # The repo's own template placeholders are not a real identity.
  [ "$name" = "GITHUBFULLNAME" ] && name=""
  [ "$email" = "GITHUBEMAIL" ] && email=""
  [ "$ghuser" = "GITHUBUSER" ] && ghuser=""

  if [ -z "$name" ] && [ -z "$email" ] && [ -z "$ghuser" ]; then
    ok "no personal git identity baked into the tracked .gitconfig — nothing to migrate"
    return 0
  fi

  if [ -e "$local_cfg" ] || [ -L "$local_cfg" ]; then
    if [ ! -f "$local_cfg" ] || [ -L "$local_cfg" ]; then
      error "cannot safely update ~/.gitconfig.local: it exists but is not a regular file"
      return 1
    fi
  fi

  # Checkout restores the index, so it cannot clean a staged personal identity.
  if ! git diff --cached --quiet --no-ext-diff --no-textconv -- homedir/.gitconfig; then
    error "staged changes in homedir/.gitconfig; preserve or unstage them before migrating identity"
    return 1
  fi

  tmp_cfg=$(mktemp "$HOME/.gitconfig.local.XXXXXX") || {
    error "could not create a private temporary git-config file; migration stopped"
    return 1
  }
  if [ -f "$local_cfg" ] && ! cat "$local_cfg" >"$tmp_cfg"; then
    rm -f "$tmp_cfg"
    error "could not preserve the existing ~/.gitconfig.local; migration stopped"
    return 1
  fi
  {
    printf '\n# Local git identity — NOT tracked by the dotfiles repo.\n' &&
    printf '# Migrated by update.sh; edit freely. Pulled in via [include] in ~/.gitconfig.\n' &&
    printf '[user]\n' &&
    { [ -z "$name" ] || printf '\tname = %s\n' "$name"; } &&
    { [ -z "$email" ] || printf '\temail = %s\n' "$email"; } &&
    { [ -z "$ghuser" ] || { printf '[github]\n' && printf '\tuser = %s\n' "$ghuser"; }; }
  } >>"$tmp_cfg" || {
    rm -f "$tmp_cfg"
    error "could not write the migrated identity; migration stopped"
    return 1
  }

  # Back up and verify the tracked file before changing either copy.
  BACKUP_DIR="$HOME/.dotfiles_backup/$(date +%Y.%m.%d.%H.%M.%S)"
  if ! mkdir -p "$HOME/.dotfiles_backup" || ! mkdir "$BACKUP_DIR"; then
    rm -f "$tmp_cfg"
    error "could not create a new backup directory; migration stopped before changing identity files"
    return 1
  fi
  if ! cp -p "$tracked" "$BACKUP_DIR/.gitconfig" || ! cmp -s "$tracked" "$BACKUP_DIR/.gitconfig"; then
    rm -f "$tmp_cfg"
    error "git-config backup failed verification; migration stopped before changing identity files"
    return 1
  fi
  if [ -f "$local_cfg" ]; then
    original_local_backup="$BACKUP_DIR/.gitconfig.local"
    if ! cp -p "$local_cfg" "$original_local_backup" || ! cmp -s "$local_cfg" "$original_local_backup"; then
      rm -f "$tmp_cfg"
      error "local git-config backup failed verification; migration stopped before changing identity files"
      return 1
    fi
  fi

  running "moving the tracked git identity to ~/.gitconfig.local"
  if ! mv "$tmp_cfg" "$local_cfg"; then
    rm -f "$tmp_cfg"
    error "could not install ~/.gitconfig.local; tracked identity remains unchanged"
    return 1
  fi

  # The tracked file may carry other personal tweaks (e.g. includeIf blocks).
  # The verified copy above is recoverable if checkout fails.
  if ! git checkout -- homedir/.gitconfig; then
    # Git can update the tracked file and then fail in a post-checkout hook.
    # Restore the verified copy before undoing the new local identity.
    if [ "$tracked_was_symlink" -eq 1 ] || [ -L "$tracked" ] || [ -d "$tracked" ]; then
      error "checkout failed; restore homedir/.gitconfig from $BACKUP_DIR/.gitconfig before retrying"
      return 1
    fi
    if ! rollback_tmp=$(mktemp "$ROOT_DIR/homedir/.gitconfig-rollback.XXXXXX"); then
      error "checkout failed; restore homedir/.gitconfig from $BACKUP_DIR/.gitconfig before retrying"
      return 1
    fi
    if ! cp -p "$BACKUP_DIR/.gitconfig" "$rollback_tmp" || ! cmp -s "$BACKUP_DIR/.gitconfig" "$rollback_tmp" || ! mv "$rollback_tmp" "$tracked"; then
      rm -f "$rollback_tmp"
      error "checkout failed; restore homedir/.gitconfig from $BACKUP_DIR/.gitconfig before retrying"
      return 1
    fi
    rollback_tmp=""
    # Undo installing the local identity so a retry does not mistake a partial
    # migration for a completed one. Preserve any pre-existing local config.
    if [ -n "$original_local_backup" ]; then
      if ! rollback_tmp=$(mktemp "$HOME/.gitconfig.local.XXXXXX"); then
        error "could not create a rollback file; restore ~/.gitconfig.local from $original_local_backup"
      elif ! cp -p "$original_local_backup" "$rollback_tmp" || ! mv "$rollback_tmp" "$local_cfg"; then
        rm -f "$rollback_tmp"
        error "could not restore ~/.gitconfig.local; its previous contents are at $original_local_backup"
      fi
    elif ! rm -f "$local_cfg"; then
      error "could not remove the partially migrated ~/.gitconfig.local; remove it before retrying"
    fi
    error "could not restore homedir/.gitconfig; verified backup retained at $BACKUP_DIR/.gitconfig"
    return 1
  fi
  ok
  warn "backed up your previous homedir/.gitconfig to $BACKUP_DIR/.gitconfig"
  warn "if it had other personal git settings (e.g. includeIf blocks), copy them into ~/.gitconfig.local"
}

action "checking for migrations"
if ! migrate_gitconfig_identity; then
  error "identity migration failed; stopping before pulling or running the installer"
  exit 1
fi

# ###########################################################
# Pull the latest code safely
# ###########################################################
action "pulling the latest dotfiles"

origin_url=$(git remote get-url origin 2>/dev/null)
if [ -n "$origin_url" ]; then
  # redact any embedded credentials (https://user:token@host/...) before printing
  ok "origin: $(printf '%s' "$origin_url" | sed -E 's|://[^@/]+@|://***@|')"
fi

current_branch=$(git symbolic-ref -q --short HEAD 2>/dev/null)
if [ -n "$current_branch" ] && [ "$current_branch" != "main" ] && [ "$current_branch" != "master" ]; then
  if ! git diff --quiet || ! git diff --cached --quiet || [ -n "$(git ls-files --others --exclude-standard)" ]; then
    warn "stashing local changes on '$current_branch' so we can update from main"
    if git stash push -u -m "dotfiles-update-$(date +%s)" >/dev/null 2>&1; then
      STASHED_BRANCH="$current_branch"
    fi
  fi
  warn "switching from '$current_branch' to the main branch to update"
  git checkout main 2>/dev/null || git checkout master 2>/dev/null || true
fi

if ! git pull --rebase --autostash; then
  error "git pull failed. Resolve the issue shown above, then re-run ./update.sh"
  [ -n "$STASHED_BRANCH" ] && error "your stashed changes from '$STASHED_BRANCH' are safe — see: git stash list"
  exit 1
fi
ok "latest changes pulled"

running "updating submodules"
git submodule update --init --recursive >/dev/null 2>&1 || warn "submodule update reported issues (continuing)"
ok

# ###########################################################
# Re-run the installer interactively to apply the update
# ###########################################################
bot "re-running install.sh to apply the update (it's idempotent and will prompt you)…"
if ./install.sh; then
  bot "✅ dotfiles update complete!"
else
  error "install.sh exited with an error — review the output above and re-run ./install.sh when ready"
fi

# ###########################################################
# Recovery hints
# ###########################################################
[ -n "$BACKUP_DIR" ] && ok "your previous homedir/.gitconfig is backed up at: $BACKUP_DIR/.gitconfig"
if [ -n "$STASHED_BRANCH" ]; then
  ok "your local changes from '$STASHED_BRANCH' were stashed; restore with: git checkout '$STASHED_BRANCH' && git stash pop"
fi
ok "tip: reload your shell to pick up changes — exec \$SHELL -l"
