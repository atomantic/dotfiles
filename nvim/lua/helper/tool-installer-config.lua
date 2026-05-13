local M = {}

M.lsp_servers = {
  bashls = {},
  cssls = {},
  html = {},
  vimls = {},
}

M.ensure_installed = {
  -- Formatters
  'prettier',
  'stylua',
  'shfmt',
  'mdformat',

  -- Linters
  'eslint_d',
  'hadolint',
  'jsonlint',
  'markdownlint-cli2',
  'shellcheck',
  'yamllint',

  -- Debug adapters and utility CLIs
  'debugpy',
  'jq',
  'yq',
}

return M
