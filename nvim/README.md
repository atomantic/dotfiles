# Neovim setup

This configuration is a LazyVim-based setup with a small local layer for daily-driver tooling.

## Architecture

- `init.lua` boots `lua/config/lazy.lua`.
- `lazyvim.json` tracks enabled LazyVim extras.
- `lua/config/` contains global options, keymaps, and autocmds.
- `lua/plugins/` contains local plugin overrides.
- `lua/helper/tool-installer-config.lua` contains the bounded Mason tool inventory.

LazyVim owns the core LSP, diagnostics, completion, formatting, linting, and keymap behavior.
Local files should extend LazyVim through plugin `opts` instead of replacing LazyVim plugin
`config` callbacks.

## LSP and tools

- LSP servers are configured through `neovim/nvim-lspconfig` `opts.servers`.
- General command-line tools are installed through `mason-tool-installer.nvim`.
- Formatting uses `conform.nvim`.
- Linting uses `nvim-lint`.
- Python uses LazyVim's Python extra with Pyright and Ruff.
- Ruby is configured to use Solargraph through LazyVim's Ruby extra.

LspSaga and lsp-zero are intentionally not part of the active setup.

## Verification

Run the smoke check after changing the Neovim config or updating plugins:

```bash
./nvim/check_config.sh
```

The check runs Neovim headlessly against the repo config, checks representative filetypes, and verifies
the expected plugin specs. It sets `NVIM_DOTFILES_CHECK=1`, which prevents the local Mason tool installer
from starting automatic installs during the check.
