#!/usr/bin/env bash

# This script installs packages from software list files grouped by package type.

DOTFILES_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd -P)"
cd "$DOTFILES_DIR" || exit 1

# shellcheck disable=SC1091
source "$DOTFILES_DIR/lib_sh/echos.sh"
# shellcheck disable=SC1091
source "$DOTFILES_DIR/lib_sh/requirers.sh"

MODE="install"
PROFILE="combined"
SOFTWARE_DIR="${SOFTWARE_DIR:-$DOTFILES_DIR/software}"

for arg in "$@"; do
  case "$arg" in
    --check|--validate)
      MODE="check"
      ;;
    combined|all|common|private|business)
      PROFILE="$arg"
      ;;
    -*)
      error "Unexpected option '$arg'. Usage: ./install_packages.sh [--check] [software_dir] [profile]"
      exit 1
      ;;
    *)
      if [[ -d "$arg" ]]; then
        SOFTWARE_DIR="$(cd "$arg" && pwd -P)"
      else
        error "Unexpected argument '$arg'. Usage: ./install_packages.sh [--check] [software_dir] [profile]"
        exit 1
      fi
      ;;
  esac
done

case "$PROFILE" in
  combined|all|common|private|business)
    ;;
  *)
    error "Unknown profile '$PROFILE'. Expected combined, common, private, or business."
    exit 1
    ;;
esac

overlay_dirs=()
case "$PROFILE" in
  private)
    overlay_dirs=("private")
    ;;
  business)
    overlay_dirs=("business")
    ;;
  combined|all)
    overlay_dirs=("private" "business")
    ;;
  common)
    overlay_dirs=()
    ;;
esac

trim_manifest_line() {
  local line="$1"

  line="${line%%#*}"
  line="${line#"${line%%[![:space:]]*}"}"
  line="${line%"${line##*[![:space:]]}"}"

  printf '%s' "$line"
}

manifest_has_entries() {
  local file="$1"
  local raw line

  [[ -f "$file" ]] || return 1

  while IFS= read -r raw || [[ -n "$raw" ]]; do
    line="$(trim_manifest_line "$raw")"
    [[ -n "$line" ]] && return 0
  done < "$file"

  return 1
}

manifest_files_for_type() {
  local type="$1"
  local files=("$SOFTWARE_DIR/$type.list")
  local overlay

  for overlay in "${overlay_dirs[@]}"; do
    files+=("$SOFTWARE_DIR/$overlay/$type.list")
  done

  printf '%s\n' "${files[@]}"
}

require_root_manifest() {
  local type="$1"
  local file="$SOFTWARE_DIR/$type.list"

  if [[ ! -f "$file" ]]; then
    error "Missing required software manifest: $file"
    return 1
  fi
}

validate_manifest_line() {
  local type="$1"
  local line="$2"
  local source_file="$3"
  local part1 part2 part3

  case "$type" in
    tap|npm|gem|vscode)
      if [[ "$line" == *"|"* ]]; then
        error "Malformed $type entry in $source_file: $line"
        return 1
      fi
      part1="$(trim_manifest_line "$line")"
      if [[ -z "$part1" ]]; then
        error "Malformed $type entry in $source_file: package name is required"
        return 1
      fi
      ;;
    cask)
      IFS='|' read -r part1 part2 part3 <<< "$line"
      part1="$(trim_manifest_line "$part1")"
      part2="$(trim_manifest_line "${part2:-}")"
      if [[ -z "$part1" || -n "${part3:-}" || ( "$line" == *"|"* && -z "$part2" ) ]]; then
        error "Malformed cask entry in $source_file: $line"
        return 1
      fi
      ;;
    brew)
      IFS='|' read -r part1 part2 part3 <<< "$line"
      part1="$(trim_manifest_line "$part1")"
      part2="$(trim_manifest_line "${part2:-}")"
      if [[ -z "$part1" || -n "${part3:-}" || ( "$line" == *"|"* && -z "$part2" ) ]]; then
        error "Malformed brew entry in $source_file: $line"
        return 1
      fi
      ;;
    mas)
      IFS='|' read -r part1 part2 part3 <<< "$line"
      part1="$(trim_manifest_line "$part1")"
      part2="$(trim_manifest_line "${part2:-}")"
      if [[ -z "$part1" || -z "$part2" || -n "${part3:-}" || ! "$part2" =~ ^[0-9]+$ ]]; then
        error "Malformed mas entry in $source_file: $line"
        return 1
      fi
      ;;
    *)
      error "Unknown package type '$type' in $source_file"
      return 1
      ;;
  esac

  return 0
}

provider_available() {
  local type="$1"
  local label="$2"

  case "$type" in
    npm)
      if ! command -v mise >/dev/null 2>&1; then
        warn "skipping $label; mise is not installed"
        return 1
      fi
      ;;
    mas)
      if ! command -v mas >/dev/null 2>&1; then
        warn "skipping $label; mas is not installed"
        return 1
      fi
      ;;
    vscode)
      if ! command -v code >/dev/null 2>&1; then
        warn "skipping $label; VS Code command line tool 'code' is not installed"
        return 1
      fi
      ;;
  esac

  return 0
}

install_manifest_line() {
  local type="$1"
  local line="$2"
  local source_file="$3"
  local name options id part2 part3

  validate_manifest_line "$type" "$line" "$source_file" || return 1

  if [[ "$MODE" == "check" ]]; then
    return 0
  fi

  case "$type" in
    tap)
      require_tap "$line" || return 1
      ;;
    brew)
      IFS='|' read -r name options part3 <<< "$line"
      if [[ -n "${part3:-}" ]]; then
        error "Malformed brew entry in $source_file: $line"
        return 1
      fi
      name="$(trim_manifest_line "$name")"
      options="$(trim_manifest_line "${options:-}")"
      if [[ -n "$options" ]]; then
        require_brew "$name" "$options" || return 1
      else
        require_brew "$name" || return 1
      fi
      ;;
    cask)
      IFS='|' read -r name options part3 <<< "$line"
      if [[ -n "${part3:-}" ]]; then
        error "Malformed cask entry in $source_file: $line"
        return 1
      fi
      name="$(trim_manifest_line "$name")"
      options="$(trim_manifest_line "${options:-}")"
      if [[ -n "$options" ]]; then
        require_cask "$name" "$options" || return 1
      else
        require_cask "$name" || return 1
      fi
      ;;
    npm)
      require_npm "$line" || return 1
      ;;
    gem)
      require_gem "$line" || return 1
      ;;
    mas)
      IFS='|' read -r name id part3 <<< "$line"
      if [[ -n "${part3:-}" ]]; then
        error "Malformed mas entry in $source_file: $line"
        return 1
      fi
      name="$(trim_manifest_line "$name")"
      id="$(trim_manifest_line "$id")"
      require_mas "$name" "$id" || return 1
      ;;
    vscode)
      require_vscode "$line" || return 1
      ;;
  esac
}

process_manifest_file() {
  local type="$1"
  local file="$2"
  local raw line

  [[ -f "$file" ]] || return 0

  while IFS= read -r raw || [[ -n "$raw" ]]; do
    line="$(trim_manifest_line "$raw")"
    [[ -z "$line" ]] && continue
    install_manifest_line "$type" "$line" "$file" || return 1
  done < "$file"
}

install_type() {
  local type="$1"
  local label="$2"
  local file
  local active_files=()
  local response="n"

  require_root_manifest "$type" || return 1

  while IFS= read -r file; do
    if manifest_has_entries "$file"; then
      active_files+=("$file")
    fi
  done < <(manifest_files_for_type "$type")

  if [[ ${#active_files[@]} -eq 0 ]]; then
    action "skipping $label (no packages defined)"
    return 0
  fi

  if [[ "$MODE" == "check" ]]; then
    action "validating $label manifests"
    for file in "${active_files[@]}"; do
      process_manifest_file "$type" "$file" || return 1
    done
    ok
    return 0
  fi

  if [[ -n ${CI:-} ]]; then
    action "skipping $label in CI"
    return 0
  fi

  provider_available "$type" "$label" || return 0

  read -r -p "Do you want to install $label? [y|N] " response
  if [[ ! $response =~ (yes|y|Y) ]]; then
    action "skipping $label installation"
    return 0
  fi

  action "installing $label"
  for file in "${active_files[@]}"; do
    process_manifest_file "$type" "$file" || return 1
  done
  ok
}

if [[ "$MODE" == "check" ]]; then
  bot "Validating software manifests for profile: $PROFILE"
else
  bot "Installing packages for profile: $PROFILE"
fi

install_type "tap" "Homebrew taps" || exit 1
install_type "brew" "Homebrew utilities" || exit 1
install_type "cask" "Homebrew desktop apps" || exit 1
install_type "npm" "NPM global packages" || exit 1
install_type "mas" "Mac App Store apps" || exit 1
install_type "gem" "Ruby gems" || exit 1
install_type "vscode" "VS Code extensions" || exit 1

if [[ "$MODE" == "check" ]]; then
  ok "Software manifest validation complete for profile: $PROFILE"
else
  ok "Package installation complete for profile: $PROFILE"
fi
