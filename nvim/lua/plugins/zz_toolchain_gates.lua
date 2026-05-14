local toolchain = require('helper.toolchain')

return {
  {
    'mason-org/mason.nvim',
    opts = function(_, opts)
      opts.ensure_installed = toolchain.filter_mason_tools(opts.ensure_installed)
      return opts
    end,
  },
  {
    'mason-org/mason-lspconfig.nvim',
    opts = function(_, opts)
      opts.ensure_installed = toolchain.filter_names(opts.ensure_installed, toolchain.lsp_servers)
      return opts
    end,
  },
  {
    'neovim/nvim-lspconfig',
    opts = function(_, opts)
      opts.servers = toolchain.gate_lsp_servers(opts.servers)
      return opts
    end,
  },
  {
    'stevearc/conform.nvim',
    opts = function(_, opts)
      opts.formatters_by_ft = toolchain.filter_formatters_by_ft(opts.formatters_by_ft)
      return opts
    end,
  },
  {
    'mfussenegger/nvim-lint',
    opts = function(_, opts)
      opts.linters_by_ft = toolchain.filter_linters_by_ft(opts.linters_by_ft)
      return opts
    end,
  },
}
