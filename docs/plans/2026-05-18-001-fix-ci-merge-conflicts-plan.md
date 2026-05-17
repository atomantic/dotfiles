---
title: "fix: Resolve CI branch merge conflicts"
type: fix
status: completed
date: 2026-05-18
---

<!-- markdownlint-disable MD013 MD025 MD032 -->

# fix: Resolve CI branch merge conflicts

## Summary

Resolve the current `ci/github-workflows` merge conflicts against `main` by preserving the branch's CI coverage while keeping `main` as the source of truth for the newer software-manifest and mise installer flow. The conflict set is limited to the three workflow files plus `.gitignore`, `homedir/.gitconfig`, and `install.sh`.

---

## Problem Frame

The branch added CI workflows and CI-mode installer behavior before `main` advanced the installer into the software-manifest and mise-based setup. A direct merge now produces add/add workflow conflicts and content conflicts in shared installer/config files.

---

## Requirements

- R1. Resolve all merge conflicts between `ci/github-workflows` and `main` without dropping CI coverage added by the branch.
- R2. Preserve `main`'s current software-manifest installer contract, including `./install.sh ./software` and manifest validation.
- R3. Preserve CI non-interactive installer behavior so GitHub Actions can exercise bootstrap without hanging on prompts or GUI operations.
- R4. Keep local-only AI/runtime workspace ignores intact without reintroducing tracked `.serena` state.
- R5. Keep Git configuration consistent with `main`'s removal of stale commit-template wiring while preserving current branch-local alias updates only when still valid.
- R6. Leave unrelated divergent work, including Neovim/plugin changes and generated logs, out of the conflict-resolution patch unless Git requires mechanical merge results.

---

## Scope Boundaries

- This plan does not redesign the CI workflows beyond resolving the conflict and preserving branch/main intent.
- This plan does not migrate package inventory further than `main` already does.
- This plan does not refresh hosts, Neovim plugins, lockfiles, or submodules as part of conflict resolution.
- This plan does not remove the user's untracked local files.

### Deferred to Follow-Up Work

- Audit whether generated files such as `nvim.log` and old conflict artifacts should be removed from `main`: separate cleanup after the merge conflict is resolved.
- Reconcile package-source naming drift such as OpenCode tap names across `Brewfile`, `packages.json`, and software manifests: separate package-maintenance hardening pass.

---

## Context & Research

### Relevant Code and Patterns

- `git merge-tree --name-only HEAD main` reports conflicts in `.github/workflows/bootstrap.yml`, `.github/workflows/reliability-gates.yml`, `.github/workflows/syntax-gate.yml`, `.gitignore`, `homedir/.gitconfig`, and `install.sh`.
- `.github/workflows/bootstrap.yml` exists on both sides; `main` adds pull-request triggers and runs `bash install.sh ./software`, while the branch runs `bash install.sh`.
- `.github/workflows/reliability-gates.yml` exists on both sides; `main` adds software manifest validation to the branch's shellcheck and symlink/Brewfile gates.
- `.github/workflows/syntax-gate.yml` exists on both sides; the branch validates JSON files, while `main` validates software manifests.
- `install.sh` on `main` already combines CI prompt-skipping with checkout-relative paths, `lib_sh/mise_setup.sh`, `SOFTWARE_DIR`, safer nvim symlink handling, mise setup, and `install_packages.sh` manifest execution.
- `homedir/.gitconfig` on `main` removes the stale commit-template setting and has a newer oh-my-zsh commit-alias hash than the branch.

### Institutional Learnings

- This repo's durable maintenance posture is review-first: reason from `install.sh`, `install_packages.sh`, `Brewfile`, and package manifests rather than turning bootstrap into blind package upgrades.
- `.compound-engineering/solutions/` is the repo-local location for durable solution notes; docs under `docs/` are not the AI learning artifact store.

### External References

- No external research needed. This is a repo-local merge conflict plan and the required behavior is visible in the branch, `main`, and repo guidance.

---

## Key Technical Decisions

- Prefer `main` for installer and package-management source of truth: `main` has the newer manifest and mise migration, so conflict resolution should apply CI-specific behavior on top of that shape instead of restoring the branch's older asdf/nvm path.
- Combine workflow trigger and validation intent: keep pull-request triggers from `main`, keep branch CI jobs, and include both JSON validation and software-manifest validation where they cover different surfaces.
- Keep `.gitignore` as the union of local AI/runtime ignores, but avoid duplicating patterns or reintroducing tracked `.serena` files.
- Keep `homedir/.gitconfig` closer to `main`: the stale commit template was removed there, and the branch's templateDir comment is not required to resolve the CI merge.

---

## Open Questions

### Resolved During Planning

- Merge target: the user confirmed `main` as the target for the current `ci/github-workflows` branch.
- Conflict posture: the user confirmed that `main` should remain the package/runtime source of truth while the branch's CI behavior is preserved.

### Deferred to Implementation

- Final oh-my-zsh commit-alias hash: resolve to the merged submodule state after Git completes the merge, then verify the config is parseable.
- Exact lockfile merge result: accept Git's non-conflicting merge unless verification surfaces an actual plugin break.

---

## Implementation Units

### U1. Resolve Workflow Add/Add Conflicts

**Goal:** Produce merged workflow files that preserve branch CI coverage and `main`'s newer manifest-aware gates.

**Requirements:** R1, R2, R3

**Dependencies:** None

**Files:**
- Modify: `.github/workflows/bootstrap.yml`
- Modify: `.github/workflows/reliability-gates.yml`
- Modify: `.github/workflows/syntax-gate.yml`
- Test: `.github/workflows/bootstrap.yml`
- Test: `.github/workflows/reliability-gates.yml`
- Test: `.github/workflows/syntax-gate.yml`

**Approach:**
- In `bootstrap.yml`, keep push, pull-request, and manual triggers, and run the bootstrap with the explicit software manifest directory.
- In `reliability-gates.yml`, keep the shellcheck and stow/Brewfile checks and retain `main`'s software manifest validation.
- In `syntax-gate.yml`, preserve bash and zsh syntax checks, keep JSON validation from the branch, and add software manifest validation from `main` as a separate gate.
- Keep matrix and environment settings conservative; do not add new runner versions or package manager behavior while resolving the conflict.

**Patterns to follow:**
- Existing workflow style in `.github/workflows/bootstrap.yml`
- Existing validation command shape in `.github/workflows/reliability-gates.yml`
- Repo package manifest contract in `software/README.md`

**Test scenarios:**
- Happy path: a pull request to `main` triggers all three workflows.
- Happy path: bootstrap CI calls the installer with an explicit software manifest directory and does not fall back to the legacy implicit package source.
- Happy path: syntax gate checks shell syntax, validates JSON files, and validates software manifests when `software/` exists.
- Error path: invalid software manifest content causes the manifest validation step to fail.
- Integration: reliability gates exercise stow and manifest validation in the same workflow without one masking the other.

**Verification:**
- The three workflow YAML files parse as valid YAML.
- GitHub Actions shows the expected workflow triggers for pull requests and pushes.
- The workflow files contain no conflict markers.

---

### U2. Reconcile Installer Conflict Around CI and Manifest Runtime

**Goal:** Keep `install.sh` aligned with `main`'s checkout-relative mise/software-manifest implementation while preserving CI non-interactive behavior.

**Requirements:** R2, R3

**Dependencies:** U1

**Files:**
- Modify: `install.sh`
- Test: `install.sh`
- Test: `install_packages.sh`

**Approach:**
- Use `main`'s checkout-relative `DOTFILES_DIR`, `SOFTWARE_DIR`, and `lib_sh/mise_setup.sh` sourcing as the base.
- Preserve CI defaults that skip prompts, GUI opens, `p10k configure`, killall loops, and blind final package upgrades.
- Preserve the explicit software manifest directory argument handling so workflow bootstrap can call the installer with `./software`.
- Keep the `install_packages.sh` call manifest-aware and profile-aware; do not restore the older asdf/nvm or implicit package source behavior.

**Patterns to follow:**
- Path handling in `install.sh` on `main`
- Manifest validation and profile handling in `install_packages.sh`
- Existing helper style from `lib_sh/echos.sh`, `lib_sh/requirers.sh`, and `lib_sh/mise_setup.sh`

**Test scenarios:**
- Happy path: running installer syntax checks on the merged file succeeds.
- Happy path: `CI=true` bootstrap path does not wait for interactive prompt input.
- Happy path: passing an existing software manifest directory selects that directory for package installation.
- Error path: passing a non-existent software manifest directory exits with a clear error before package installation starts.
- Integration: the bootstrap workflow's install step matches the installer argument contract.

**Verification:**
- `install.sh` has no conflict markers and remains executable.
- Shell syntax validation passes for `install.sh`.
- The installer references `mise_setup.sh` and `install_packages.sh` consistently with `main`'s manifest flow.

---

### U3. Resolve Local Ignore and Git Config Conflicts

**Goal:** Merge `.gitignore` and `homedir/.gitconfig` without losing local-only workspace protections or restoring stale Git configuration.

**Requirements:** R4, R5

**Dependencies:** None

**Files:**
- Modify: `.gitignore`
- Modify: `homedir/.gitconfig`
- Test: `.gitignore`
- Test: `homedir/.gitconfig`

**Approach:**
- Keep the managed agent workspace ignore block and local AI/runtime state entries.
- Remove duplicate ignore entries introduced by both sides.
- Keep `main`'s removal of the stale commit template setting.
- Resolve the oh-my-zsh commit-alias value after the submodule merge result is known rather than preserving an obsolete hash by accident.

**Patterns to follow:**
- Existing `# BEGIN idream-managed agent workspace` block structure in `.gitignore`
- Existing Git config section ordering in `homedir/.gitconfig`

**Test scenarios:**
- Happy path: local generated state under known AI/runtime directories remains ignored.
- Happy path: `homedir/.gitconfig` parses with Git config tooling after conflict resolution.
- Edge case: duplicated ignore patterns do not obscure the managed block or create contradictory unignore rules.
- Error path: no reference remains to a deleted commit template file.

**Verification:**
- `.gitignore` and `homedir/.gitconfig` contain no conflict markers.
- Git config parsing succeeds for `homedir/.gitconfig`.
- `git check-ignore` confirms representative local-only workspace paths are ignored.

---

### U4. Final Merge Hygiene and Focused Validation

**Goal:** Confirm the merge resolution is clean, scoped, and does not accidentally include unrelated divergent work.

**Requirements:** R1, R6

**Dependencies:** U1, U2, U3

**Files:**
- Test: `.github/workflows/bootstrap.yml`
- Test: `.github/workflows/reliability-gates.yml`
- Test: `.github/workflows/syntax-gate.yml`
- Test: `.gitignore`
- Test: `homedir/.gitconfig`
- Test: `install.sh`
- Test: `install_packages.sh`

**Approach:**
- Check for conflict markers across the repository, while recognizing old tracked artifacts may already contain historical marker text and should not be treated as newly introduced unless touched by the merge.
- Verify shell syntax and manifest validation for the conflict-related surfaces.
- Review the final diff against `main` to ensure conflict-resolution work does not pull in unrelated hosts, Neovim, lockfile, or generated-log cleanup.
- Keep user untracked files untouched.

**Patterns to follow:**
- Existing repo guidance that there is no formal test suite.
- Existing workflow-level validation rather than adding a new test framework.

**Test scenarios:**
- Happy path: all conflict files are resolved and no files remain in unmerged status.
- Happy path: focused syntax and manifest checks pass on the merged tree.
- Edge case: historical conflict markers in untouched artifacts are noted separately and not mixed into this conflict-resolution patch.
- Integration: the final CI workflow set validates installer, shell syntax, GitHub workflow syntax, JSON, Brewfile, stow symlinks, and software manifests.

**Verification:**
- Git reports no unmerged paths.
- The final merge diff contains only expected conflict-resolution output plus Git-required non-conflicting merge results.
- Focused checks pass locally, and remote CI can run on the resulting branch.

---

## System-Wide Impact

- **Interaction graph:** The resolved workflows call `install.sh`, which calls `install_packages.sh`; this must stay aligned with the software manifest contract.
- **Error propagation:** Manifest validation failures should fail CI directly rather than being hidden behind warning-only checks.
- **State lifecycle risks:** CI should continue removing conflicting runner dotfiles before stow, while local installs should not delete user files unexpectedly.
- **API surface parity:** The installer CLI contract used by humans and CI should remain the same: optional software manifest directory plus profile selection through `install_packages.sh`.
- **Integration coverage:** Workflow validation is the main cross-layer proof because it covers GitHub Actions, shell scripts, stow, Homebrew parsing, and software manifests together.
- **Unchanged invariants:** No package manager should perform blind upgrade behavior because of the conflict resolution; CI should remain non-interactive.

---

## Risks & Dependencies

| Risk | Mitigation |
| ------ | ------------ |
| Restoring the older asdf/nvm installer path from the branch | Use `main`'s `install.sh` as the base and reapply only CI-specific conflict intent. |
| Dropping branch workflow coverage while accepting `main` wholesale | Resolve workflow add/add conflicts by combining triggers and validation steps. |
| Accidentally committing unrelated generated or local files | Review final status and diff after conflict resolution; keep untracked local files untouched. |
| Treating historical conflict markers in unrelated artifacts as new merge residue | Scope marker cleanup to files touched by the merge unless a separate cleanup is requested. |
| CI differs from local macOS behavior | Keep CI defaults explicit and verify both shell syntax and workflow-level contracts. |

---

## Documentation / Operational Notes

- No README update is required for this conflict-resolution patch unless implementation discovers that the final workflow behavior differs from the documented install commands.
- If this merge exposes stale tracked generated files, document that as follow-up cleanup rather than expanding the conflict-resolution scope.

---

## Sources & References

- Related branch: `ci/github-workflows`
- Merge target: `main`
- Conflict detector: `git merge-tree --name-only HEAD main`
- Related code: `.github/workflows/bootstrap.yml`
- Related code: `.github/workflows/reliability-gates.yml`
- Related code: `.github/workflows/syntax-gate.yml`
- Related code: `.gitignore`
- Related code: `homedir/.gitconfig`
- Related code: `install.sh`
- Related code: `install_packages.sh`
- Related code: `software/README.md`
