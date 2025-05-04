-- Keymaps are automatically loaded on the VeryLazy event
-- Default keymaps that are always set: https://github.com/LazyVim/LazyVim/blob/main/lua/lazyvim/config/keymaps.lua
-- Add any additional keymaps here

local wk = require('which-key')
wk.add({
  { '<leader>p', group = 'Project management' },
})

vim.keymap.set('n', '<leader>ps', vim.cmd.ProjectRoot, { desc = 'Set project root' })

-- Copilot Keymaps

vim.keymap.set('n', '<leader>ac', ':CopilotChatModels<CR>', { desc = 'Chat Models' })

-- Navbuddy
