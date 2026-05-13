return {
  { 'nvim-telescope/telescope-media-files.nvim' },
  { 'nvim-telescope/telescope-ui-select.nvim' },
  {
    'nvim-telescope/telescope.nvim',
    dependencies = {
      'nvim-lua/plenary.nvim',
    },
    opts = function(_, opts)
      opts.extensions = vim.tbl_deep_extend('force', opts.extensions or {}, {
        ['ui-select'] = require('telescope.themes').get_dropdown({}),
        media_files = {
          filetypes = { 'png', 'webp', 'jpg', 'jpeg', 'pdf' },
          find_cmd = 'rg',
        },
      })
    end,
    keys = {
      { '<C-p>', '<cmd>Telescope git_files<CR>', desc = 'Find Git Files' },
      { '<leader>fm', '<cmd>Telescope media_files<CR>', desc = 'Find Media Files' },
    },
    config = function(_, opts)
      require('telescope').setup(opts)
      pcall(require('telescope').load_extension, 'ui-select')
      pcall(require('telescope').load_extension, 'media_files')
    end,
  },
}
