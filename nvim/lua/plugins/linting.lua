return {
  {
    'mfussenegger/nvim-lint',
    opts = function(_, opts)
      opts.linters_by_ft = opts.linters_by_ft or {}
      opts.linters_by_ft.bash = { 'shellcheck' }
      opts.linters_by_ft.sh = { 'shellcheck' }
      opts.linters_by_ft.zsh = { 'shellcheck' }
      opts.linters_by_ft.dockerfile = { 'hadolint' }
      opts.linters_by_ft.json = { 'jsonlint' }
      opts.linters_by_ft.yaml = { 'yamllint' }
      return opts
    end,
  },
}
