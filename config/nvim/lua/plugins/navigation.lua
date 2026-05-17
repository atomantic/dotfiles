-- local navbuddy = require('nvim-navbuddy')
--
-- require('lspconfig').eslint_ls.setup({
--   on_attach = function(client, bufnr)
--     navbuddy.attach(client, bufnr)
--   end,
-- })

return {
  {
    'christoomey/vim-tmux-navigator',
    cmd = {
      'TmuxNavigateLeft',
      'TmuxNavigateDown',
      'TmuxNavigateUp',
      'TmuxNavigateRight',
      'TmuxNavigatePrevious',
      'TmuxNavigatorProcessList',
    },
    keys = {
      { '<c-h>', '<cmd><C-U>TmuxNavigateLeft<cr>' },
      { '<c-j>', '<cmd><C-U>TmuxNavigateDown<cr>' },
      { '<c-k>', '<cmd><C-U>TmuxNavigateUp<cr>' },
      { '<c-l>', '<cmd><C-U>TmuxNavigateRight<cr>' },
      { '<c-\\>', '<cmd><C-U>TmuxNavigatePrevious<cr>' },
    },
  },
  {
    'neovim/nvim-lspconfig',
    dependencies = {
      {
        'SmiteshP/nvim-navbuddy',
        dependencies = {
          'SmiteshP/nvim-navic',
          'MunifTanjim/nui.nvim',
        },
        opts = { lazy = true, lsp = { auto_attach = true } },
        keys = {
          { '<leader>cn', '<cmd>Navbuddy<CR>', { desc = 'Open Navbuddy' } },
        },
      },
    },
  },
}
