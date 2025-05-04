-- Reduced version of lsp-zero in case I want to take over myself due to lsp-zero side effects
-- Originally from github.com/cpow/neovim-for-newbs/
-- if true then return {} end

return {
  {
    'williamboman/mason.nvim',
    lazy = false,
    config = function()
      require('mason').setup()
    end,
  },
  {
    'williamboman/mason-lspconfig.nvim',
    lazy = false,
    opts = {
      auto_install = true,
    },
  },
  {
    'neovim/nvim-lspconfig',
    lazy = false,
    config = function()
      local capabilities = require('cmp_nvim_lsp').default_capabilities()

      local lspconfig = require('lspconfig')
      lspconfig.tsserver.setup({ capabilities = capabilities })
      lspconfig.solargraph.setup({ capabilities = capabilities })
      lspconfig.html.setup({ capabilities = capabilities })
      lspconfig.lua_ls.setup({ capabilities = capabilities })

      vim.keymap.set('n', 'K', vim.lsp.buf.hover, {})
      vim.keymap.set('n', '<leader>gd', vim.lsp.buf.definition, {})
      vim.keymap.set('n', '<leader>gr', vim.lsp.buf.references, {})
      vim.keymap.set('n', '<leader>ca', vim.lsp.buf.code_action, {})
    end,
  },
  {
    'WhoIsSethDaniel/mason-tool-installer.nvim',
    config = function()
      require('mason-tool-installer').setup({
        ensure_installed = {
          'ast-grep',
          'chrome-debug-adapter',
          'css-lsp',
          'eslint-lsp',
          'cucumber-language-server',
          'reformat-gherkin',
          'html-lsp',
          'lua-language-server',
          'json-lsp',
          'markdownlint-cli2',
          'mdformat',
          'prettier',
          'shellcheck',
          'shfmt',
          'sonarlint-language-server',
          'stylelint-lsp',
          'stylua',
          'typescript-language-server',
          'vim-language-server',
          'vtsls',
          'yaml-language-server',
          'yq',

          -- python
          'black',
          'debugpy',
          'flake8',
          'isort',
          'pyright',
          'python-lsp-server',

          -- docker
          'docker-compose-language-service',
          'dockerfile-language-server',
        },
        run_on_start = true,
        start_delay = 3000,
        debounce_hours = 5,
      })
      vim.api.nvim_create_autocmd('User', {
        pattern = 'MasonToolsStartingInstall',
        callback = function()
          vim.schedule(function()
            print('mason-tool-installer is starting')
          end)
        end,
      })
      vim.api.nvim_create_autocmd('User', {
        pattern = 'MasonToolsUpdateCompleted',
        callback = function(e)
          vim.schedule(function()
            print('mason-tool-installer updated:\n' .. vim.inspect(e.data))
          end)
        end,
      })
    end,
  },
}
