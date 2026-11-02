#!/usr/bin/env bash

###########################
# This script restores backed up dotfiles
# @author Adam Eivy
###########################

# Resolve paths from this script so it can be launched from any directory.
ROOT_DIR="$(CDPATH= cd -- "$(dirname "${BASH_SOURCE[0]}")" && pwd)" || exit 1
source "$ROOT_DIR/lib_sh/echos.sh" || exit 1
source "$ROOT_DIR/lib_sh/requirers.sh" || exit 1

if [[ -z $1 || "$1" == */* || "$1" == "." || "$1" == ".." ]]; then
  error "specify a backup folder name from ~/.dotfiles_backup/ (not a path)."
  exit 1
fi

BACKUP_DIR="$HOME/.dotfiles_backup/$1"
if [[ ! -d "$BACKUP_DIR" || -L "$BACKUP_DIR" ]]; then
  error "backup folder does not exist or is a symlink: $BACKUP_DIR"
  exit 1
fi

bot "Do you wish to change your shell back to bash?"
read -r -p "[Y|n] " response

if [[ $response =~ ^(no|n|N) ]];then
    bot "ok, leaving shell as zsh..."
else
    bot "ok, changing shell to bash..."
    chsh -s "$(command -v bash)" || exit 1
fi

bot "Restoring dotfiles from backup..."

# The installer stores dotfiles inside the selected backup directory. Restore
# only those entries, and never follow or delete an unrelated HOME entry.
restore_failed=0
for backup_file in "$BACKUP_DIR"/.[!.]* "$BACKUP_DIR"/..?*; do
  # Bash leaves an unmatched glob unchanged; skip it and any non-restorable entry.
  [[ -e "$backup_file" || -L "$backup_file" ]] || continue

  file="${backup_file##*/}"
  target="$HOME/$file"
  running "$target"

  if [[ -e "$target" || -L "$target" ]]; then
    if [[ ! -L "$target" ]]; then
      error "refusing to overwrite existing non-symlink: $target (backup retained)"
      restore_failed=1
      continue
    fi
    link_target="$(readlink "$target")" || {
      error "could not inspect symlink: $target (backup retained)"
      restore_failed=1
      continue
    }
    if [[ "$link_target" != "$ROOT_DIR/homedir/$file" && "$link_target" != "$HOME/.dotfiles/homedir/$file" ]]; then
      # The same managed directory may have different logical paths (for
      # example /tmp and /private/tmp), or the link may be relative to HOME.
      link_parent="$(dirname "$link_target")"
      [[ "$link_target" == /* ]] || link_parent="$HOME/$link_parent"
      link_parent="$(CDPATH= cd -P -- "$link_parent" 2>/dev/null && pwd -P)" || link_parent=""
      managed_parent="$(CDPATH= cd -P -- "$ROOT_DIR/homedir" 2>/dev/null && pwd -P)" || managed_parent=""
      if [[ "${link_target##*/}" != "$file" || -z "$link_parent" || "$link_parent" != "$managed_parent" ]]; then
        error "refusing to remove unmanaged symlink: $target (backup retained)"
        restore_failed=1
        continue
      fi
    fi
    rm "$target" || {
      error "could not remove managed symlink: $target (backup retained)"
      restore_failed=1
      continue
    }
  fi

  if mv "$backup_file" "$target"; then
    echo -en "$1 backup restored";ok
    echo -en '\done';ok
  else
    error "could not restore $file; backup retained at $backup_file"
    restore_failed=1
  fi
done

if [[ $restore_failed -ne 0 ]]; then
  error "some entries were not restored; their backups were retained"
  exit 1
fi

bot "Woot! All done."
