-- vim.keymaps are automatically loaded on the VeryLazy event
-- Default vim.keymaps that are always set: https://github.com/LazyVim/LazyVim/blob/main/lua/lazyvim/config/keymaps.lua
-- Add any additional vim.keymaps here

local Util = require('lazyvim.util')
local wk = require('which-key')
wk.add({
  { '<leader>p', group = 'Project management' },
})

vim.keymap.set('n', '<leader>ps', vim.cmd.ProjectRoot, { desc = 'Set project root' })

-- Copilot vim.keymaps

vim.keymap.set('n', '<leader>ac', ':CopilotChatModels<CR>', { desc = 'Chat Models' })

-- LspSaga
if Util.has('lspsaga.nvim') then
  vim.notify('LspSaga enabled')
  -- LSP finder - Find the symbol's definition
  vim.keymap.set('n', 'gh', '<cmd>Lspsaga lsp_finder<CR>', { desc = 'LSPSaga: Finder' })

  -- Code action
  vim.keymap.set({ 'n', 'v' }, 'ca', '<cmd>Lspsaga code_action<CR>', { desc = 'LSPSaga: Code Action' })

  -- Rename all occurrences of the hovered word for the entire file
  vim.keymap.set('n', 'cr', '<cmd>Lspsaga rename<CR>', { desc = 'LSPSaga: Rename' })

  -- Rename all occurrences of the hovered word for the selected files
  vim.keymap.set('n', 'cR', '<cmd>Lspsaga rename ++project<CR>', { desc = 'LSPSaga: Proj Rename' })

  -- Peek definition
  vim.keymap.set('n', 'gp', '<cmd>Lspsaga peek_definition<CR>', { desc = 'LSPSaga: Peek Def' })

  -- Go to definition
  vim.keymap.set('n', 'gD', '<cmd>Lspsaga goto_definition<CR>', { desc = 'LSPSaga: Goto Def' })

  -- Go to type definition
  vim.keymap.set('n', 'gt', '<cmd>Lspsaga goto_type_definition<CR>', { desc = 'LSPSaga: Goto TypeDef' })

  -- Diagnostic jump can use `<c-o>` to jump back
  vim.keymap.set('n', '[e', '<cmd>Lspsaga diagnostic_jump_prev<CR>')
  vim.keymap.set('n', ']e', '<cmd>Lspsaga diagnostic_jump_next<CR>')

  -- Diagnostic jump with filters such as only jumping to an error
  vim.keymap.set('n', '[E', function()
    require('lspsaga.diagnostic'):goto_prev({ severity = vim.diagnostic.severity.ERROR })
  end)
  vim.keymap.set('n', ']E', function()
    require('lspsaga.diagnostic'):goto_next({ severity = vim.diagnostic.severity.ERROR })
  end)

  -- Toggle Outline
  vim.keymap.set('n', '<leader>o', '<cmd>Lspsaga outline<CR>', { desc = 'LSPSaga: Outline' })

  -- Pressing the key twice will enter the hover window
  vim.keymap.set('n', 'K', '<cmd>Lspsaga hover_doc<CR>', { desc = 'LSPSaga: HoverDoc' })
end

-- Navbuddy
