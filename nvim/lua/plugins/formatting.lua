local toolchain = require('helper.toolchain')

return {
  {
    'stevearc/conform.nvim',
    opts = function(_, opts)
      opts.formatters_by_ft = opts.formatters_by_ft or {}
      opts.formatters_by_ft.bash = { 'shfmt' }
      opts.formatters_by_ft.sh = { 'shfmt' }
      opts.formatters_by_ft.zsh = { 'shfmt' }
      opts.formatters_by_ft = toolchain.filter_formatters_by_ft(opts.formatters_by_ft)

      opts.formatters = opts.formatters or {}
      opts.formatters.shfmt = vim.tbl_deep_extend('force', opts.formatters.shfmt or {}, {
        prepend_args = { '-i', '2', '-ci' },
      })

      return opts
    end,
  },
}
