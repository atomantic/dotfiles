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
        opts = { lazy = false, lsp = { auto_attach = true } },
        keys = {
          { '<leader>cn', '<cmd>Navbuddy<CR>', { desc = 'Open Navbuddy' } },
        },
        -- enabling the configuration somehow breaks the logic, with and without the keymapj
        -- config = function()
        -- vim.keymap.set('n', '<leader>cn', ':Navbuddy<CR>', { desc = 'Open Navbuddy' })
        --   local navbuddy = require('nvim-navbuddy')
        --   local actions = require('nvim-navbuddy.actions')
        --
        --   navbuddy.setup({
        --     use_default_mappings = true,
        --   })
        -- end,
      },
    },
  },
  -- {
  --   'SmiteshP/nvim-navic',
  --   config = function()
  --     local navic = require('nvim-navic')
  --     navic.setup({
  --       highlight = true,
  --       lsp = {
  --         auto_attach = true,
  --       },
  --     })
  --   end,
  -- },
  --   {
  --     'rebelot/heirline.nvim',
  --     config = function()
  --       require('heirline').setup({})
  --     end,
  --   },
}
