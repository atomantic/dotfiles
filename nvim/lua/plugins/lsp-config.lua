local tools = require('helper.tool-installer-config')
local is_check = vim.env.NVIM_DOTFILES_CHECK == '1'

return {
  {
    'mason-org/mason.nvim',
    config = is_check and function()
      require('mason').setup({})
    end or nil,
    opts = function(_, opts)
      if is_check then
        opts.ensure_installed = {}
      end
    end,
  },
  {
    'mason-org/mason-lspconfig.nvim',
    enabled = not is_check,
  },
  {
    'neovim/nvim-lspconfig',
    opts = function(_, opts)
      if is_check then
        opts.servers = { ['*'] = opts.servers and opts.servers['*'] or {} }
        return
      end

      opts.servers = vim.tbl_deep_extend('force', opts.servers or {}, tools.lsp_servers)
    end,
  },
  {
    'WhoIsSethDaniel/mason-tool-installer.nvim',
    enabled = not is_check,
    event = 'VeryLazy',
    dependencies = { 'mason-org/mason.nvim' },
    config = function()
      require('mason-tool-installer').setup({
        ensure_installed = tools.ensure_installed,
        run_on_start = not is_check,
        start_delay = 3000,
        debounce_hours = 12,
      })
      vim.api.nvim_create_autocmd('User', {
        pattern = 'MasonToolsStartingInstall',
        callback = function()
          vim.schedule(function()
            vim.notify('Mason tool installer is starting', vim.log.levels.INFO)
          end)
        end,
      })
      vim.api.nvim_create_autocmd('User', {
        pattern = 'MasonToolsUpdateCompleted',
        callback = function(e)
          vim.schedule(function()
            vim.notify('Mason tools updated: ' .. vim.inspect(e.data), vim.log.levels.INFO)
          end)
        end,
      })
    end,
  },
}
