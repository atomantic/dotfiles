return {
  { 'iagorrr/noctishc.nvim' },
  {
    'LazyVim/LazyVim',
    opts = {
      -- colorscheme = 'tokyonight',
      colorscheme = 'catppuccin-mocha',
    },
  },
  {
    'catppuccin/nvim',
    name = 'catppuccin',
    opts = {
      flavour = 'auto',
      integrations = {
        cmp = true,
        gitsigns = true,
        nvimtree = true,
        telescope = true,
        -- ...add more integrations as needed
      },
      dim_inactive = { enabled = false, shade = 'dark', percentage = 0.15 },
      styles = {
        comments = { 'italic' },
      },
    },
  },
  {
    'folke/tokyonight.nvim',
    name = 'tokyonight',
    opts = {
      style = 'night', -- or "moon", "storm", "night", "day"
    },
  },
}
