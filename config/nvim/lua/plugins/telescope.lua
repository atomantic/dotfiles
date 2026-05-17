return {
  { 'nvim-telescope/telescope-media-files.nvim' },
  { 'nvim-telescope/telescope-ui-select.nvim' },
  {
    'nvim-telescope/telescope.nvim',
    tag = '0.1.8',
    dependencies = {
      'nvim-lua/plenary.nvim',
    },
    config = function()
      require('telescope').setup({
        extensions = {
          ['ui-select'] = { require('telescope.themes').get_dropdown({}) },
          media_files = {
            -- filetypes whitelist
            filetypes = { 'png', 'webp', 'jpg', 'jpeg', 'pdf' },
            -- find command (defaults to `fd`)
            find_cmd = 'rg',
          },
        },
      })

      local builtin = require('telescope.builtin')
      -- the following is not necessary because its already mapped to <leader><space>
      -- vim.keymap.set("n", "<leader>ff", builtin.find_files, { desc = "[F]ind in [F]iles" })
      vim.keymap.set('n', '<C-p>', builtin.git_files, { desc = 'Find in git files' })
      vim.keymap.set('n', '<leader>fg', builtin.live_grep, { desc = '[F]ind with [G]rep' })
      vim.keymap.set('n', '<leader>fb', builtin.buffers, { desc = '[F]ind in [B]uffers' })
      vim.keymap.set('n', '<leader>fh', builtin.help_tags, { desc = '[F]ind in [H]elp tags' })
      vim.keymap.set('n', '<leader>fm', ':Telescope media_files<CR>', { desc = '[F]ind [m]edia files' })

      require('telescope').load_extension('ui-select')
      require('telescope').load_extension('media_files')
    end,
  },
}
