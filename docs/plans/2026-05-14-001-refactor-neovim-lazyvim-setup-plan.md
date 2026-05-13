---
title: "refactor: Solidify Neovim LazyVim Setup"
type: refactor
status: completed
date: 2026-05-14
---

# refactor: Solidify Neovim LazyVim Setup

## Summary

Refactor the Neovim configuration around LazyVim's native extension points, keeping the experimental branch's useful direction while
avoiding a wholesale merge. The plan removes LspSaga and active lsp-zero usage, centralizes tool installation, simplifies language
configuration, and adds a repeatable verification path for future updates.

---

## Problem Frame

The current Neovim setup mixes LazyVim defaults, lsp-zero-era configuration, disabled examples, and branch experiments. That makes it
harder to know which layer owns LSP behavior, formatter behavior, keymaps, and Mason-managed tooling.

---

## Requirements

- R1. The final setup should use LazyVim as the owner of core plugin, LSP, diagnostic, keymap, formatting, and linting behavior.
- R2. LspSaga should not be included as an active plugin or keymap layer.
- R3. lsp-zero should not participate in the active LSP setup.
- R4. Mason-managed tool installation should be centralized and bounded to daily-driver languages/tools rather than a speculative maximal list.
- R5. Python support should remain strong, using LazyVim's Python extra and explicit local overrides only where they add clear value.
- R6. Formatting and linting should prefer LazyVim's current defaults: conform.nvim for formatting and nvim-lint for linting.
- R7. The configuration should be easier to update, debug, and verify after LazyVim/plugin upgrades.
- R8. Stale examples, disabled plugin specs, and misleading docs should be removed or rewritten so the repo reflects the actual setup.

---

## Scope Boundaries

- Do not introduce LspSaga.
- Do not build a fully custom Neovim distribution; keep this a LazyVim-based config.
- Do not maximize every possible language server. Start with the languages represented by the current config and the useful subset from
  `origin/marc-improved-lsp-setup`.
- Do not turn this into a full CI project unless the verification script reveals enough value to wire it later.
- Do not migrate the broader dotfiles repository structure as part of this work.

### Deferred to Follow-Up Work

- CI integration for the Neovim smoke check: defer until the local verification script is stable and useful.
- More opinionated DAP/test-runner workflow: defer until the baseline LSP/format/lint setup is stable.
- Project-specific Python policy for mypy or bandit: defer unless there is a clear project-local need; global defaults should stay low-noise.

---

## Context & Research

### Relevant Code and Patterns

- `nvim/lua/config/lazy.lua` imports LazyVim and local plugin specs. It currently imports the none-ls extra directly in code while
  `nvim/lazyvim.json` also tracks extras.
- `nvim/lua/plugins/lsp-config.lua` manually sets up Mason, mason-lspconfig, nvim-lspconfig, capabilities, and several servers.
- `nvim/lua/plugins/lsp-zero.lua` still contains an active lsp-zero-based setup.
- `nvim/lua/plugins/python.lua` adds Python-specific Pyright, Ruff formatting, nvim-lint, and Mason tool installer behavior.
- `nvim/lua/plugins/lspsaga.lua` adds LspSaga and `nvim/lua/config/keymaps.lua` maps several Saga commands when the plugin exists.
- `origin/marc-improved-lsp-setup` removes LspSaga and Python-specific file duplication, adds centralized tool installation, and moves
  toward explicit LSP setup, but it also duplicates LazyVim responsibilities and leaves stale/debug state.
- `origin/feat/tuckr-migration` removes `nvim/` entirely and is not relevant to this plan.

### Institutional Learnings

- No `docs/solutions/` directory exists in this repo.
- `AGENTS.md` emphasizes idempotent dotfiles, shell/Lua style consistency, and scoped changes.

### External References

- LazyVim LSP docs: https://www.lazyvim.org/plugins/lsp
- LazyVim formatting docs: https://www.lazyvim.org/plugins/formatting
- LazyVim linting docs: https://www.lazyvim.org/plugins/linting
- LazyVim Python extra: https://www.lazyvim.org/extras/lang/python
- LazyVim news on conform.nvim, nvim-lint, and none-ls: https://www.lazyvim.org/news
- mason-lspconfig README: https://github.com/mason-org/mason-lspconfig.nvim
- mason-tool-installer README: https://github.com/WhoIsSethDaniel/mason-tool-installer.nvim
- none-ls README: https://github.com/nvimtools/none-ls.nvim

---

## Key Technical Decisions

- LazyVim-native LSP configuration: configure servers through LazyVim/nvim-lspconfig options instead of replacing the full `config` callback.
- No LspSaga layer: rely on LazyVim's built-in LSP UI, Snacks/Telescope/Trouble flows, and standard keymaps.
- Retire active lsp-zero: keep no active lsp-zero plugin spec or dependency unless a later rollback need is proven.
- Centralize tools, not behavior: one local tool inventory should feed Mason installation, while LazyVim remains responsible for
  attaching LSPs and formatters.
- Prefer conform.nvim and nvim-lint over none-ls by default: use none-ls only for a clearly missing source that cannot be handled
  cleanly elsewhere.
- Keep global Python defaults conservative: Pyright or basedpyright plus Ruff are default candidates; mypy and bandit are better as
  project-local or opt-in checks.
- Add verification as repository behavior: a small headless Neovim check gives future dependency updates a concrete pass/fail signal.

---

## Open Questions

### Resolved During Planning

- Should LspSaga be retained? Resolved: no.
- Should the experimental branch be merged wholesale? Resolved: no; salvage the useful direction and rebuild against LazyVim patterns.

### Deferred to Implementation

- Exact final Mason package names: confirm against the current Mason registry and installed plugin versions during implementation.
- Exact LazyVim extra set: start from current daily-driver needs, then adjust if LazyVim reports duplicate or obsolete extras.
- Whether none-ls remains at all: decide after mapping current format/lint sources to conform.nvim and nvim-lint.

---

## Implementation Units

### U1. Normalize LazyVim Extras and Plugin Inventory

**Goal:** Make the plugin inventory intentional and remove stale or misleading setup paths before changing behavior.

**Requirements:** R1, R2, R3, R8

**Dependencies:** None

**Files:**
- Modify: `nvim/lazyvim.json`
- Modify: `nvim/lua/config/lazy.lua`
- Modify/Delete: `nvim/lua/plugins/lsp-zero.lua`
- Delete: `nvim/lua/plugins/lspsaga.lua`
- Modify/Delete: `nvim/lua/plugins/disabled.example.lua`
- Modify/Delete: `nvim/lua/plugins/disabled.none-ls.lua`
- Modify: `nvim/lazy-lock.json`

**Approach:**
- Treat `nvim/lazyvim.json` as the intended source of LazyVim extras.
- Remove LspSaga from the active plugin set.
- Remove active lsp-zero from the plugin graph, including lockfile residue after plugin sync.
- Delete or drastically reduce starter/example files that no longer describe the local setup.
- Keep extras that match the daily-driver language/tooling profile; avoid adding branch-only extras unless there is a clear current use.

**Patterns to follow:**
- LazyVim starter convention in `nvim/lua/config/lazy.lua`
- Repo style in existing Lua plugin specs under `nvim/lua/plugins/`

**Test scenarios:**
- Test expectation: none -- this unit is plugin inventory cleanup. Behavioral verification happens through U6's headless smoke check.

**Verification:**
- Lazy can load the plugin graph without resolving `lspsaga.nvim` or `lsp-zero.nvim`.
- `nvim/lazy-lock.json` no longer contains removed active plugins after sync.
- No active config path advertises LspSaga or lsp-zero as part of the current setup.

---

### U2. Rebuild LSP Setup as LazyVim-Native Overrides

**Goal:** Replace hand-rolled LSP setup with LazyVim-compatible options so LazyVim owns diagnostics, capabilities, server setup, and
global keymap behavior.

**Requirements:** R1, R3, R7

**Dependencies:** U1

**Files:**
- Modify: `nvim/lua/plugins/lsp-config.lua`
- Optional Create: `nvim/lua/plugins/lsp.lua`
- Modify: `nvim/lua/config/options.lua`

**Approach:**
- Convert manual server setup into `neovim/nvim-lspconfig` options.
- Keep custom server settings only where they are genuinely local decisions, such as Lua runtime behavior or a C# server command that
  LazyVim does not cover cleanly.
- Avoid overriding LazyVim's core LSP `config` callback.
- Remove default debug logging from normal startup.
- Let LazyVim provide global LSP keymaps unless a local override is intentional and documented by description.

**Patterns to follow:**
- LazyVim LSP `opts.servers` pattern from current LazyVim docs.
- Existing local Lua style: 2-space indentation, single quotes.

**Test scenarios:**
- Happy path: opening Lua config attaches `lua_ls` and exposes LazyVim's standard LSP actions.
- Happy path: opening TypeScript/Vue/JSON/YAML/Python buffers attaches only the intended server set for each filetype.
- Edge case: a buffer for a language without a configured server should still open without LSP setup errors.
- Error path: missing external language server should produce a Mason/LSP notification, not a Lua startup exception.

**Verification:**
- `:LspInfo` shows expected clients for representative filetypes.
- No startup path calls lsp-zero.
- Neovim starts without Lua errors after plugin sync.

---

### U3. Centralize Mason Tool Installation with a Bounded Profile

**Goal:** Keep one clear inventory of tools to install while avoiding the experimental branch's broad speculative list.

**Requirements:** R4, R7

**Dependencies:** U2

**Files:**
- Create: `nvim/lua/helper/tool-installer-config.lua`
- Modify: `nvim/lua/plugins/lsp-config.lua`
- Optional Create: `nvim/lua/plugins/mason-tools.lua`

**Approach:**
- Create a small local inventory split by purpose: language servers, formatters, linters, and debug adapters.
- Start with current daily-driver coverage: Lua, shell, JavaScript/TypeScript, Vue, JSON, YAML, Markdown, Python, Docker,
  Tailwind/CSS/HTML, Nix, and C# if still wanted.
- Use Mason package names or lspconfig names consistently based on the active integration path.
- Keep `mason-tool-installer.nvim` focused on installation; do not use it as a second LSP configuration system.
- Avoid installing expensive or niche tools globally unless the current setup already clearly depends on them.

**Patterns to follow:**
- `origin/marc-improved-lsp-setup` centralized tool inventory idea, but with a smaller list.
- mason-tool-installer integration rules from its README.

**Test scenarios:**
- Happy path: the tool installer receives one de-duplicated list and installs missing configured tools.
- Edge case: a tool listed in both LSP and non-LSP categories is de-duplicated before install.
- Error path: an unavailable Mason package fails in Mason tooling without breaking Neovim startup.

**Verification:**
- `:MasonToolsInstall` or the configured startup path targets the bounded list.
- Duplicate entries are removed.
- Tool installation does not also manually attach LSP servers outside LazyVim's LSP flow.

---

### U4. Align Formatting, Linting, and Python with LazyVim Defaults

**Goal:** Preserve useful Python behavior while reducing duplicated formatter/linter setup and avoiding none-ls as a default dependency.

**Requirements:** R5, R6, R7

**Dependencies:** U2, U3

**Files:**
- Modify: `nvim/lua/plugins/python.lua`
- Optional Create: `nvim/lua/plugins/formatting.lua`
- Optional Create: `nvim/lua/plugins/linting.lua`
- Modify: `nvim/lua/config/options.lua`
- Modify: `nvim/lazyvim.json`

**Approach:**
- Use LazyVim's Python extra as the baseline.
- Configure Python LSP and Ruff preferences through supported LazyVim globals/options rather than duplicating the full default spec.
- Move formatter choices into conform.nvim options.
- Move linter choices into nvim-lint options only when a global default is low-noise.
- Remove none-ls sources that conform.nvim or nvim-lint already cover.
- Keep mypy and bandit out of global defaults unless implementation proves they are expected for your daily workflow.

**Patterns to follow:**
- LazyVim formatting docs for conform.nvim option extension.
- LazyVim linting docs for nvim-lint option extension.
- LazyVim Python extra docs for Python LSP/Ruff selection.

**Test scenarios:**
- Happy path: formatting a Python buffer uses the intended Ruff/conform path.
- Happy path: formatting Lua and shell buffers still uses `stylua` and `shfmt`.
- Edge case: a Python project without local mypy or bandit configuration does not receive noisy global diagnostics from those tools.
- Integration: LazyVim format info reports conform.nvim as the active formatter path for supported buffers.

**Verification:**
- `:LazyFormatInfo` shows expected formatter sources for Lua, shell, Python, Markdown, and TypeScript.
- `:LintInfo` or equivalent lint inspection shows expected lint sources only where configured.
- none-ls is absent unless a specific retained source requires it.

---

### U5. Clean Keymaps, Options, and Navigation Boundaries

**Goal:** Remove duplicated or conflicting UX configuration so LazyVim, tmux navigation, Telescope, and project-root behavior coexist predictably.

**Requirements:** R1, R2, R7, R8

**Dependencies:** U1, U2

**Files:**
- Modify: `nvim/lua/config/keymaps.lua`
- Modify: `nvim/lua/config/options.lua`
- Modify: `nvim/lua/plugins/navigation.lua`
- Modify: `nvim/lua/plugins/telescope.lua`
- Modify: `nvim/lua/plugins/lualine.lua`

**Approach:**
- Remove LspSaga-specific keymaps and avoid remapping LazyVim's standard LSP keys without a clear reason.
- Resolve duplicate window navigation mappings between raw `wincmd` mappings and `vim-tmux-navigator`.
- Revisit forced `cd` behavior against LazyVim root detection; prefer LazyVim/project-root mechanisms unless manual cwd behavior is
  explicitly desired.
- Keep Telescope customizations that add real workflow value, but avoid overriding LazyVim defaults wholesale.
- Remove startup/debug/performance options that are speculative or can make behavior surprising.

**Patterns to follow:**
- LazyVim keymap conventions and `which-key` grouping.
- Existing `vim-tmux-navigator` plugin spec in `nvim/lua/plugins/navigation.lua`.

**Test scenarios:**
- Happy path: standard LazyVim LSP keymaps work in a buffer with an attached server.
- Happy path: tmux-pane and Neovim-window navigation works with the chosen navigation layer.
- Edge case: opening Neovim outside a git repo does not force an unexpected project root failure.
- Integration: Telescope keymaps still work without replacing LazyVim's default picker behavior.

**Verification:**
- `:map`/which-key output does not show dead LspSaga bindings.
- Navigation keys have one clear owner.
- Project root behavior matches the documented choice in the README.

---

### U6. Add Documentation and Repeatable Verification

**Goal:** Make the improved setup understandable and give future plugin updates a concrete smoke-check path.

**Requirements:** R7, R8

**Dependencies:** U1, U2, U3, U4, U5

**Files:**
- Modify: `nvim/README.md`
- Create: `nvim/check_config.sh`
- Optional Modify: `scripts/README.md`

**Approach:**
- Rewrite the Neovim README around the actual architecture: LazyVim base, local plugin specs, centralized Mason tools,
  conform/nvim-lint, and no LspSaga/lsp-zero.
- Add a small headless verification script that checks Neovim startup, plugin spec loading, and representative health surfaces without
  needing a full test framework.
- Document how to run the smoke check before and after LazyVim/plugin updates.
- Keep the script idempotent and compatible with shell style in this repo.

**Patterns to follow:**
- Shell script conventions from `AGENTS.md`.
- Existing Neovim install helper scripts under `nvim/`.

**Test scenarios:**
- Happy path: running the verification script against a synced plugin set exits successfully.
- Error path: a Lua startup error or missing plugin spec causes the script to fail clearly.
- Integration: representative filetype checks exercise at least Lua, shell, Python, TypeScript/JavaScript, Markdown, and JSON/YAML
  support at a smoke-test level.

**Verification:**
- The README describes the current setup rather than LazyVim starter defaults.
- The script provides a reusable check for future dependency updates.
- The plan's key behavioral claims can be verified without manual inspection alone.

---

## System-Wide Impact

- **Interaction graph:** Plugin specs, LazyVim extras, Mason-managed tools, LSP server attachment, formatters, linters, keymaps, and
  README documentation all become aligned around one architecture.
- **Error propagation:** Startup Lua errors and missing Mason packages should be visible through Neovim/Lazy/Mason health paths, not
  hidden by duplicated setup layers.
- **State lifecycle risks:** Lockfile updates can remove stale plugins or introduce new dependency versions; review
  `nvim/lazy-lock.json` carefully after sync.
- **API surface parity:** There is no public API, but user-facing Neovim keymaps and commands are part of the daily workflow and should
  be treated as stable.
- **Integration coverage:** The smoke check should exercise startup and representative filetypes because Lua syntax checks alone will
  not prove plugin integration.
- **Unchanged invariants:** The repository remains a macOS dotfiles project using GNU Stow for `homedir/`; this plan only changes the
  Neovim configuration area.

---

## Risks & Dependencies

| Risk | Mitigation |
|------|------------|
| LazyVim API drift breaks copied config patterns | Use LazyVim documented `opts` extension points and avoid replacing core `config` callbacks. |
| Mason package names differ from lspconfig names | Confirm names during implementation and keep tool inventory comments explicit. |
| Removing lsp-zero changes completion/LSP behavior unexpectedly | Verify representative filetypes and rely on LazyVim's nvim-cmp/LSP defaults. |
| Removing none-ls drops a formatter or diagnostic source | Map each retained source to conform.nvim or nvim-lint before deleting none-ls. |
| Global Python linting becomes noisy | Keep mypy/bandit out of global defaults unless explicitly needed. |
| Keymap cleanup changes muscle memory | Prefer LazyVim defaults and document intentional local overrides. |

---

## Documentation / Operational Notes

- Update `nvim/README.md` in the same change as the config cleanup so future updates have accurate guidance.
- Run the verification script before updating `nvim/lazy-lock.json` and after plugin syncs.
- Keep `origin/marc-improved-lsp-setup` as reference material only; do not treat it as an implementation branch to merge.

---

## Sources & References

- Related branch: `origin/marc-improved-lsp-setup`
- Current config entrypoint: `nvim/init.lua`
- LazyVim setup: `nvim/lua/config/lazy.lua`
- Current LSP setup: `nvim/lua/plugins/lsp-config.lua`
- Current lsp-zero setup: `nvim/lua/plugins/lsp-zero.lua`
- Current Python setup: `nvim/lua/plugins/python.lua`
- Current LspSaga setup: `nvim/lua/plugins/lspsaga.lua`
- Current keymaps: `nvim/lua/config/keymaps.lua`
- External docs: https://www.lazyvim.org/plugins/lsp
- External docs: https://www.lazyvim.org/plugins/formatting
- External docs: https://www.lazyvim.org/plugins/linting
- External docs: https://www.lazyvim.org/extras/lang/python
- External docs: https://www.lazyvim.org/news
- External docs: https://github.com/mason-org/mason-lspconfig.nvim
- External docs: https://github.com/WhoIsSethDaniel/mason-tool-installer.nvim
- External docs: https://github.com/nvimtools/none-ls.nvim
