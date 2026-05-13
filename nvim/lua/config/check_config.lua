local M = {}

local expected_filetypes = {
  { 'nvim/lua/config/options.lua', 'lua' },
  { 'nvim/check_config.sh', 'sh' },
  { 'nvim/README.md', 'markdown' },
  { 'nvim/lazyvim.json', 'json' },
}

local required_plugins = {
  'nvim-lspconfig',
  'conform.nvim',
  'nvim-lint',
}

local removed_plugins = {
  'lspsaga.nvim',
  'lsp-zero.nvim',
  'none-ls.nvim',
}

function M.run()
  for _, item in ipairs(expected_filetypes) do
    local path, expected = item[1], item[2]
    local actual = vim.filetype.match({ filename = path })
    assert(actual == expected, string.format('expected %s filetype for %s, got %s', expected, path, tostring(actual)))
  end

  local plugins = require('lazy.core.config').plugins

  for _, name in ipairs(required_plugins) do
    assert(plugins[name], name .. ' is not configured')
  end

  for _, name in ipairs(removed_plugins) do
    assert(not plugins[name], name .. ' should not be configured')
  end
end

function M.main()
  local ok, err = pcall(M.run)
  if not ok then
    vim.api.nvim_err_writeln(err)
    vim.cmd('cquit')
  end
end

return M
