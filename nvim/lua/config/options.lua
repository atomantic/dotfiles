-- Options are automatically loaded before lazy.nvim startup
-- Default options that are always set: https://github.com/LazyVim/LazyVim/blob/main/lua/lazyvim/config/options.lua
-- Add any additional options here
--
vim.lsp.log.set_level(vim.log.levels.OFF)

vim.g.lazyvim_python_lsp = 'pyright'
vim.g.lazyvim_python_ruff = 'ruff'
vim.g.lazyvim_ruby_lsp = 'solargraph'
vim.g.lazyvim_ruby_formatter = 'rubocop'
vim.g.lazyvim_prettier_needs_config = false

-- Set indentation options
vim.opt.autoindent = true
vim.opt.expandtab = true
vim.opt.shiftwidth = 2
vim.opt.tabstop = 2

-- Ensures that whitespaces are correctly visualised
vim.g.editorconfig = true
vim.opt.listchars = { tab = '->', trail = '-', nbsp = '+', space = '·' }

vim.opt.number = true

vim.opt.updatetime = 300 -- Faster updates for LSP/diagnostics
vim.opt.timeoutlen = 500 -- Shorter key timeout
