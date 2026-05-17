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

local function realpath(path)
  return (vim.uv or vim.loop).fs_realpath(path) or path
end

local function read_lockfile(lockfile)
  assert(vim.fn.filereadable(lockfile) == 1, 'missing lazy lockfile at ' .. lockfile)

  local data = table.concat(vim.fn.readfile(lockfile), '\n')
  local ok, lock = pcall(vim.json.decode, data)

  assert(ok and type(lock) == 'table', 'invalid lazy lockfile at ' .. lockfile)

  return lock
end

local function locked_plugin_names(plugins, lock)
  local missing = {}

  for name, plugin in pairs(plugins) do
    if not plugin._.is_local and not plugin.virtual and not lock[name] then
      table.insert(missing, name)
    end
  end

  table.sort(missing)

  return missing
end

function M.run()
  local expected_config = vim.env.NVIM_DOTFILES_CONFIG

  assert(expected_config and expected_config ~= '', 'NVIM_DOTFILES_CONFIG must point at the repo nvim config')
  assert(
    realpath(vim.fn.stdpath('config')) == realpath(expected_config),
    string.format(
      'expected active nvim config %s, got %s',
      realpath(expected_config),
      realpath(vim.fn.stdpath('config'))
    )
  )

  for _, item in ipairs(expected_filetypes) do
    local path, expected = item[1], item[2]
    local actual = vim.filetype.match({ filename = path })
    assert(actual == expected, string.format('expected %s filetype for %s, got %s', expected, path, tostring(actual)))
  end

  local lazy_config = require('lazy.core.config')
  local plugins = require('lazy.core.config').plugins
  local lock = read_lockfile(lazy_config.options.lockfile)
  local missing_lock_entries = locked_plugin_names(plugins, lock)

  assert(not lazy_config.options.install.missing, 'check mode must not install missing Lazy plugins')
  assert(
    #missing_lock_entries == 0,
    'lazy-lock.json is missing configured plugins: ' .. table.concat(missing_lock_entries, ', ')
  )

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
