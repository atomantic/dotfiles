return {
  -- Python language server and diagnostics
  {
    'neovim/nvim-lspconfig',
    opts = {
      servers = {
        pyright = {
          settings = {
            python = {
              analysis = {
                autoImportCompletions = true,
                typeCheckingMode = 'basic',
                diagnosticMode = 'workspace',
              },
            },
          },
        },
      },
    },
  },

  -- Ruff integration (linter + formatter)
  {
    'stevearc/conform.nvim',
    opts = {
      formatters_by_ft = {
        python = { 'ruff_format' },
      },
      formatters = {
        ruff_format = {
          command = 'ruff',
          args = { 'format', '--stdin-filename', '$FILENAME' },
          stdin = true,
        },
      },
    },
  },

  -- none-ls is enabled via LazyVim extra, but Python diagnostics are handled by nvim-lint
  {
    'nvimtools/none-ls.nvim',
    dependencies = {
      'nvim-lua/plenary.nvim',
    },
    opts = function(_, opts)
      return opts
    end,
  },

  -- Keybindings for Python linting/diagnostics
  {
    'folke/which-key.nvim',
    config = function()
      local wk = require('which-key')
      wk.add({
        { '<leader>p', group = 'Python' },
        { '<leader>pl', '<cmd>lua vim.diagnostic.open_float()<CR>', desc = 'Show line diagnostics' },
        { '<leader>pL', '<cmd>lua require("lint").try_lint()<CR>', desc = 'Run linters' },
        { '<leader>pd', '<cmd>lua vim.lsp.buf.definition()<CR>', desc = 'Go to definition' },
        { '<leader>ph', '<cmd>lua vim.lsp.buf.hover()<CR>', desc = 'Hover documentation' },
        { '<leader>pr', '<cmd>lua vim.lsp.buf.references()<CR>', desc = 'Find references' },
        { '<leader>pf', '<cmd>lua vim.lsp.buf.format()<CR>', desc = 'Format buffer' },
      })
    end
  },

  -- Python linting via nvim-lint (LazyVim default)
  {
    'mfussenegger/nvim-lint',
    opts = function(_, opts)
      opts.events = { 'BufWritePost' }
      opts.linters_by_ft = opts.linters_by_ft or {}
      local python_linters = opts.linters_by_ft.python or {}

      for _, linter in ipairs({ 'ruff', 'mypy', 'bandit' }) do
        if not vim.tbl_contains(python_linters, linter) then
          table.insert(python_linters, linter)
        end
      end

      opts.linters_by_ft.python = python_linters
      return opts
    end,
  },

  -- Ensure Python tools are installed via mason
  {
    'WhoIsSethDaniel/mason-tool-installer.nvim',
    opts = function(_, opts)
      opts.ensure_installed = opts.ensure_installed or {}

      if vim.fn.executable('python3') == 1 or vim.fn.executable('python') == 1 then
        for _, tool in ipairs({
          'ruff',
          'mypy',
          'bandit',
          'pyright',
          'black',
          'docformatter',
          'isort',
          'debugpy',
        }) do
          if not vim.tbl_contains(opts.ensure_installed, tool) then
            table.insert(opts.ensure_installed, tool)
          end
        end
      end

      return opts
    end,
  },
}
