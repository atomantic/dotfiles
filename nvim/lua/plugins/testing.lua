return {
  'vim-test/vim-test',
  dependencies = {
    'preservim/vimux',
  },
  config = function()
    local wk = require('which-key')
    wk.add({
      { '<leader>t', group = 'Testing' },
    })
    vim.keymap.set('n', '<leader>tt', ':TestNearest<CR>', {})
    vim.keymap.set('n', '<leader>tT', ':TestFile<CR>', {})
    vim.keymap.set('n', '<leader>ta', ':TestSuite<CR>', {})
    vim.keymap.set('n', '<leader>tl', ':TestLast<CR>', {})
    vim.keymap.set('n', '<leader>tg', ':TestVisit<CR>', {})
    vim.cmd('let test#strategy = \'vimux\'')
  end,
}
