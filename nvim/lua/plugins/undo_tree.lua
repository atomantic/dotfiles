return {
  'mbbill/undotree',
  init = function()
    vim.keymap.set('n', '<leader><F5>', vim.cmd.UndotreeToggle, { desc = 'Toggle Undo Tree' })
    vim.keymap.set('n', '<leader><F6>', vim.cmd.UndotreePersistUndo, { desc = 'Toggle persistent Undo Tree' })
  end,
}
