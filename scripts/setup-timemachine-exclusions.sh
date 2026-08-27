#!/usr/bin/env bash

set -euo pipefail

MODE="report"
SCAN_PROJECTS=1
PROJECT_ROOTS=()
TOTAL_KIB=0
INCLUDED_KIB=0
EXCLUDED_KIB=0
FOUND_COUNT=0
APPLIED_COUNT=0
FAILED_COUNT=0

usage() {
  cat <<'EOF'
Usage: setup-timemachine-exclusions.sh [options]

Audit or configure conservative Time Machine exclusions for regenerable data.
The default mode is read-only.

Options:
  --apply                Add missing exclusions with sudo tmutil.
  --report               Report candidates without changing anything (default).
  --no-project-scan      Do not search project roots for dependency/build caches.
  --project-root PATH    Scan PATH instead of the default project roots. Repeatable.
  -h, --help             Show this help.

Examples:
  ./scripts/setup-timemachine-exclusions.sh
  ./scripts/setup-timemachine-exclusions.sh --apply
  ./scripts/setup-timemachine-exclusions.sh --apply --project-root "$HOME/src"

Applying exclusions requires Terminal to have Full Disk Access. This script does
not delete files, local snapshots, or existing Time Machine backup history.
EOF
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --apply)
      MODE="apply"
      ;;
    --report)
      MODE="report"
      ;;
    --no-project-scan)
      SCAN_PROJECTS=0
      ;;
    --project-root)
      if [[ $# -lt 2 || -z "$2" ]]; then
        echo "error: --project-root requires a path" >&2
        exit 2
      fi
      PROJECT_ROOTS+=("$2")
      shift
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      echo "error: unknown option: $1" >&2
      usage >&2
      exit 2
      ;;
  esac
  shift
done

if [[ "$(uname -s)" != "Darwin" ]]; then
  echo "error: this script only supports macOS" >&2
  exit 1
fi

if ! command -v tmutil >/dev/null 2>&1; then
  echo "error: tmutil is unavailable" >&2
  exit 1
fi

if [[ ${#PROJECT_ROOTS[@]} -eq 0 ]]; then
  PROJECT_ROOTS=(
    "$HOME/github.com"
    "$HOME/gitlab.com"
  )
else
  # Explicit roots come from the caller, so a typo deserves an error instead of a
  # silently empty scan that still reports success. Canonicalize them too: find
  # echoes each root exactly as it was given, so a relative root would put
  # ambiguous, cwd-dependent paths in the report and in any exclusion added.
  for root_index in "${!PROJECT_ROOTS[@]}"; do
    root="${PROJECT_ROOTS[$root_index]}"
    if [[ ! -d "$root" ]]; then
      echo "error: --project-root is not a directory: $root" >&2
      exit 2
    fi
    PROJECT_ROOTS[root_index]="$(cd "$root" && pwd -P)"
  done
fi

is_excluded() {
  tmutil isexcluded "$1" 2>/dev/null | grep -q '^\[Excluded\]'
}

size_kib() {
  { du -x -sk "$1" 2>/dev/null || true; } | awk 'NR == 1 { print $1 + 0 }'
}

human_size_from_kib() {
  awk -v kib="$1" 'BEGIN {
    if (kib >= 1073741824) printf "%.1fT", kib / 1073741824
    else if (kib >= 1048576) printf "%.1fG", kib / 1048576
    else if (kib >= 1024) printf "%.1fM", kib / 1024
    else printf "%dK", kib
  }'
}

process_candidate() {
  local path="$1"
  local reason="$2"
  local known_status="${3:-}"
  local kib=0
  local human="unknown"
  local status="included"

  [[ -e "$path" ]] || return 0

  kib="$(size_kib "$path")"
  kib="${kib:-0}"
  human="$(human_size_from_kib "$kib")"
  human="${human:-unknown}"
  TOTAL_KIB=$((TOTAL_KIB + kib))
  FOUND_COUNT=$((FOUND_COUNT + 1))

  if [[ "$known_status" == "excluded" ]]; then
    status="excluded"
    EXCLUDED_KIB=$((EXCLUDED_KIB + kib))
  elif [[ -z "$known_status" ]] && is_excluded "$path"; then
    status="excluded"
    EXCLUDED_KIB=$((EXCLUDED_KIB + kib))
  else
    INCLUDED_KIB=$((INCLUDED_KIB + kib))
  fi

  if [[ "$MODE" == "apply" && "$status" == "included" ]]; then
    if sudo tmutil addexclusion -p "$path" >/dev/null && is_excluded "$path"; then
      status="added"
      APPLIED_COUNT=$((APPLIED_COUNT + 1))
    else
      status="FAILED"
      FAILED_COUNT=$((FAILED_COUNT + 1))
    fi
  fi

  printf '%-9s %8s  %s\n' "$status" "$human" "$path"
  printf '                     %s\n' "$reason"
}

report_filesystems() {
  local destination=""
  local data_volume="/System/Volumes/Data"

  # Pre-Catalina systems have no separate Data volume. Fall back to / there, and
  # keep df out of the pipeline's exit status: under `set -o pipefail` a failing
  # df would abort the whole audit before a single candidate is reported.
  [[ -d "$data_volume" ]] || data_volume="/"

  echo "Filesystem capacity"
  { df -h "$data_volume" 2>/dev/null || true; } | sed -n '1,2p'

  destination="$(
    { tmutil destinationinfo 2>/dev/null || true; } |
      awk -F ':[[:space:]]*' '/Mount Point/{print $2; exit}'
  )"
  if [[ -n "$destination" && -d "$destination" ]]; then
    echo
    echo "Time Machine destination"
    { df -h "$destination" 2>/dev/null || true; } | sed -n '1,2p'
  else
    echo
    echo "Time Machine destination is unavailable or not configured."
  fi
}

report_risky_existing_exclusions() {
  local path=""
  local found=0
  local paths=(
    "$HOME/.antigravity"
    "$HOME/.codex"
    "$HOME/.lmstudio"
    "$HOME/.ollama"
    "$HOME/.portos"
    "$HOME/.pyenv"
    "$HOME/.yarn"
    "$HOME/Pictures"
    "$HOME/Poliigon"
    "$HOME/googledrive"
    "$HOME/Library/Developer/CoreSimulator"
  )

  echo
  echo "Broad existing exclusions to review manually"
  echo "These can contain unique state; this script never adds or removes them."
  for path in "${paths[@]}"; do
    [[ -e "$path" ]] || continue
    if is_excluded "$path"; then
      printf 'review             %s\n' "$path"
      found=$((found + 1))
    fi
  done
  if [[ $found -eq 0 ]]; then
    echo "none detected"
  fi
}

if [[ "$MODE" == "apply" ]]; then
  echo "Requesting administrator authorization for fixed-path exclusions..."
  if ! sudo -v; then
    echo "error: administrator authorization failed" >&2
    exit 1
  fi
fi

report_filesystems

echo
echo "Conservative regenerable-data exclusions ($MODE mode)"

# General and package-manager caches. Configuration and credentials live outside
# these paths (for example ~/.npmrc and ~/.cargo/credentials.toml).
process_candidate "$HOME/.cache" "General per-user cache, including Hugging Face downloads."
process_candidate "$HOME/Library/Caches" "Standard macOS per-user application caches."
process_candidate "$HOME/.npm" "npm download cache and logs; ~/.npmrc is preserved."
process_candidate "$HOME/.pnpm-store" "Legacy pnpm content-addressed package store."
process_candidate "$HOME/Library/pnpm/store" "pnpm content-addressed package store."
process_candidate "$HOME/.yarn/cache" "Yarn package cache; project Yarn releases/plugins are preserved."
process_candidate "$HOME/.bun/install/cache" "Bun package download cache."
process_candidate "$HOME/.nvm/.cache" "Downloaded nvm source archives; installed Node versions are preserved."
process_candidate "$HOME/.pyenv/cache" "Downloaded pyenv source archives; installed Python versions are preserved."
process_candidate "$HOME/.rustup/downloads" "Downloaded Rust toolchain archives."
process_candidate "$HOME/.rustup/tmp" "Temporary rustup installation files."
process_candidate "$HOME/.swiftpm/cache" "Swift Package Manager download cache."
process_candidate "$HOME/.gradle/caches" "Gradle dependency and build caches."
process_candidate "$HOME/.gradle/daemon" "Gradle daemon state and logs."
process_candidate "$HOME/.gradle/native" "Regenerable Gradle native integration files."
process_candidate "$HOME/.gradle/notifications" "Gradle version-notification cache."
process_candidate "$HOME/.gradle/wrapper/dists" "Downloaded Gradle distributions."
process_candidate "$HOME/.cargo/registry" "Downloaded Cargo registry content."
process_candidate "$HOME/.cargo/git" "Downloaded Cargo Git dependencies."
process_candidate "$HOME/go/pkg/mod" "Downloaded Go module cache."
process_candidate "$HOME/Library/Caches/go-build" "Go compiler build cache."
process_candidate "$HOME/.terraform.d/plugin-cache" "Downloaded Terraform provider plugin cache."

# Downloaded local AI models and app-managed runtimes. User conversations,
# generated media, session banks, experiments, and source trees are preserved.
process_candidate "$HOME/.ollama/models" "Downloaded Ollama models; manifests and blobs can be fetched again."
process_candidate "$HOME/.lmstudio/models" "Downloaded LM Studio models; conversations and settings are preserved."
process_candidate "$HOME/.lmstudio/extensions/backends" "Downloaded LM Studio inference backends."
process_candidate "$HOME/.lmstudio/extensions/frameworks" "Downloaded LM Studio runtime frameworks."
process_candidate "$HOME/.lmstudio/.internal/bundled-models" "LM Studio bundled models."
process_candidate "$HOME/.mtplx/models" "Downloaded MTPLX model weights; session-bank data is preserved."
process_candidate "$HOME/.codex/cache" "Codex catalog cache; sessions, worktrees, and generated images are preserved."
process_candidate "$HOME/.codex/.tmp" "Codex temporary plugin and marketplace staging data."
process_candidate "$HOME/.codex/plugins/cache" "Downloaded Codex plugin cache; personal plugin source is preserved."
process_candidate "$HOME/Library/Application Support/Claude/vm_bundles" "Downloaded Claude VM runtime bundles."
process_candidate "$HOME/Library/Application Support/Claude/Cache" "Claude desktop HTTP cache; conversations are preserved."
process_candidate "$HOME/Library/Application Support/Claude/Code Cache" "Claude desktop compiled-code cache."
process_candidate "$HOME/Library/Application Support/Claude/GPUCache" "Claude desktop GPU cache."
process_candidate "$HOME/Library/Application Support/Google/Chrome/OptGuideOnDeviceModel" "Downloaded Chrome on-device model data."

# Apple developer build products. Archives and simulator device data are
# deliberately not excluded.
process_candidate "$HOME/Library/Developer/Xcode/DerivedData" "Rebuildable Xcode indexes and build products."
process_candidate "$HOME/Library/Developer/Xcode/DocumentationCache" "Downloaded Xcode documentation cache."
process_candidate "$HOME/Library/Developer/Xcode/iOS DeviceSupport" "Downloaded iOS device-support symbols."
process_candidate "$HOME/Library/Developer/Xcode/watchOS DeviceSupport" "Downloaded watchOS device-support symbols."
process_candidate "$HOME/Library/Developer/CoreSimulator/Caches" "CoreSimulator cache; simulator device data is preserved."
process_candidate "$HOME/Library/Developer/CoreSimulator/Temp" "CoreSimulator temporary files."

if [[ $SCAN_PROJECTS -eq 1 ]]; then
  DISCOVERED_PATHS=()
  DISCOVERED_REASONS=()
  echo
  echo "Project dependency/build-cache discovery"
  echo "Scanning configured roots; Git metadata, source, virtualenvs, and worktrees are preserved."
  for root in "${PROJECT_ROOTS[@]}"; do
    [[ -d "$root" ]] || continue
    while IFS= read -r path; do
      case "$(basename "$path")" in
        node_modules)
          DISCOVERED_PATHS+=("$path")
          DISCOVERED_REASONS+=("Installed Node dependencies, reproducible from package manifests/locks.")
          ;;
        .pnpm-store)
          DISCOVERED_PATHS+=("$path")
          DISCOVERED_REASONS+=("Project-local pnpm content-addressed package store.")
          ;;
        .build)
          DISCOVERED_PATHS+=("$path")
          DISCOVERED_REASONS+=("Swift Package Manager build products.")
          ;;
        .turbo)
          DISCOVERED_PATHS+=("$path")
          DISCOVERED_REASONS+=("Turborepo build cache.")
          ;;
        .parcel-cache)
          DISCOVERED_PATHS+=("$path")
          DISCOVERED_REASONS+=("Parcel build cache.")
          ;;
      esac
    done < <(
      find "$root" -xdev -maxdepth 6 -type d \
        \( -name .git -o -name node_modules -o -name .pnpm-store -o -name .build -o -name .turbo -o -name .parcel-cache \) \
        -prune -print 2>/dev/null
    )
  done

  if [[ ${#DISCOVERED_PATHS[@]} -gt 0 ]]; then
    DISCOVERED_INDEX=0
    while IFS= read -r status_line; do
      [[ $DISCOVERED_INDEX -lt ${#DISCOVERED_PATHS[@]} ]] || break
      DISCOVERED_STATUS="included"
      if [[ "$status_line" == "[Excluded]"* ]]; then
        DISCOVERED_STATUS="excluded"
      fi
      process_candidate \
        "${DISCOVERED_PATHS[$DISCOVERED_INDEX]}" \
        "${DISCOVERED_REASONS[$DISCOVERED_INDEX]}" \
        "$DISCOVERED_STATUS"
      DISCOVERED_INDEX=$((DISCOVERED_INDEX + 1))
    done < <(tmutil isexcluded "${DISCOVERED_PATHS[@]}" 2>/dev/null || true)

    # Fall back to individual checks if tmutil returned fewer rows than paths.
    while [[ $DISCOVERED_INDEX -lt ${#DISCOVERED_PATHS[@]} ]]; do
      process_candidate \
        "${DISCOVERED_PATHS[$DISCOVERED_INDEX]}" \
        "${DISCOVERED_REASONS[$DISCOVERED_INDEX]}"
      DISCOVERED_INDEX=$((DISCOVERED_INDEX + 1))
    done
  fi
fi

echo
printf 'Candidates found: %d\n' "$FOUND_COUNT"
awk -v kib="$TOTAL_KIB" 'BEGIN {printf "Candidate data: %.2f GiB\n", kib / 1048576}'
awk -v kib="$EXCLUDED_KIB" 'BEGIN {printf "Already excluded: %.2f GiB\n", kib / 1048576}'
awk -v kib="$INCLUDED_KIB" 'BEGIN {printf "Newly covered if applied: %.2f GiB\n", kib / 1048576}'

if [[ "$MODE" == "apply" ]]; then
  printf 'Exclusions added: %d\n' "$APPLIED_COUNT"
  printf 'Exclusions failed: %d\n' "$FAILED_COUNT"
else
  echo
  echo "No settings changed. Re-run with --apply to add missing fixed-path exclusions."
fi

report_risky_existing_exclusions

cat <<'EOF'

Important:
  - Exclusions reduce future backup growth; they do not erase existing backups.
  - A backup can fail before reaching its destination if the source Data volume
    lacks room to create a local APFS snapshot. Free local space first.
  - Cloud sync is not a backup, so iCloud Drive and other synced user data are
    intentionally not excluded here.
EOF

if [[ $FAILED_COUNT -gt 0 ]]; then
  echo "error: one or more exclusions failed; grant Terminal Full Disk Access and retry" >&2
  exit 1
fi
