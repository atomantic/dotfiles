-- Options are automatically loaded before lazy.nvim startup
-- Default options that are always set: https://github.com/LazyVim/LazyVim/blob/main/lua/lazyvim/config/options.lua
-- Add any additional options here
--
-- vim.lsp.set_log_level('debug')
vim.lsp.set_log_level('off')

-- my own configs
-- vim.cmd('set autochdir=true')
vim.cmd('cd ' .. vim.fn.getcwd())

-- Set indentation options
vim.opt.autoindent = true
vim.opt.expandtab = true
vim.opt.shiftwidth = 2
vim.opt.tabstop = 2

-- Ensures that whitespaces are correctly visualised
vim.g.editorconfig = true
vim.opt.listchars = { tab = '->', trail = '-', nbsp = '+', space = '·' }

-- Improve window pane navigation
-- from https://github.com/cpow/neovim-for-newbs/
vim.keymap.set('n', '<c-k>', ':wincmd k<CR>')
vim.keymap.set('n', '<c-j>', ':wincmd j<CR>')
vim.keymap.set('n', '<c-h>', ':wincmd h<CR>')
vim.keymap.set('n', '<c-l>', ':wincmd l<CR>')

vim.wo.number = true
