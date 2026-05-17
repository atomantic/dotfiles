return {
  -- LazyVim's Python extra owns pyright/ruff enablement. This file keeps only
  -- local preferences that differ from the extra defaults.
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

  {
    'stevearc/conform.nvim',
    opts = {
      formatters_by_ft = {
        python = { 'ruff_format', 'ruff_organize_imports' },
      },
    },
  },
}
