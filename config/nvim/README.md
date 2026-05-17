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
- Language tool installs are gated by local toolchain availability in `lua/helper/toolchain.lua`.
- Python uses LazyVim's Python extra with Pyright and Ruff.
- Ruby is configured to use Solargraph through LazyVim's Ruby extra.

LspSaga and lsp-zero are intentionally not part of the active setup.

## Verification

Run the smoke check after changing the Neovim config or updating plugins:

```bash
./nvim/check_config.sh
```

The check runs Neovim headlessly with this repo's `nvim/` directory as the active XDG config, checks
representative filetypes, verifies expected plugin specs, and confirms configured plugins are represented in
`lazy-lock.json`. It sets `NVIM_DOTFILES_CHECK=1`, which disables Lazy update checks, Lazy missing-plugin
installs, Lazy bootstrap cloning, and the local Mason tool installer during the check.
