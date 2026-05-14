local M = {}

local toolchains = {
  dotnet = { any = { 'dotnet' } },
  go = { any = { 'go' } },
  java = { any = { 'java' } },
  nix = { any = { 'nix' } },
  node = { any = { 'node' } },
  python = { any = { 'python3', 'python' } },
  ruby = { any = { 'ruby' } },
  zig = { any = { 'zig' } },
}

M.lsp_servers = {
  astro = toolchains.node,
  basedpyright = toolchains.python,
  cssls = toolchains.node,
  docker_compose_language_service = toolchains.node,
  dockerls = toolchains.node,
  eslint = toolchains.node,
  fsautocomplete = toolchains.dotnet,
  gopls = toolchains.go,
  html = toolchains.node,
  jdtls = toolchains.java,
  jsonls = toolchains.node,
  kotlin_language_server = toolchains.java,
  nil_ls = toolchains.nix,
  omnisharp = toolchains.dotnet,
  pyright = toolchains.python,
  rubocop = toolchains.ruby,
  ruby_lsp = toolchains.ruby,
  ruff = toolchains.python,
  ruff_lsp = toolchains.python,
  solargraph = toolchains.ruby,
  standardrb = toolchains.ruby,
  tailwindcss = toolchains.node,
  ts_ls = toolchains.node,
  tsgo = toolchains.node,
  tsserver = toolchains.node,
  vtsls = toolchains.node,
  vue_ls = toolchains.node,
  yamlls = toolchains.node,
  zls = toolchains.zig,
}

M.mason_tools = {
  astro = toolchains.node,
  csharpier = toolchains.dotnet,
  debugpy = toolchains.python,
  delve = toolchains.go,
  erb_formatter = toolchains.ruby,
  ['erb-formatter'] = toolchains.ruby,
  ['erb-lint'] = toolchains.ruby,
  eslint_d = toolchains.node,
  fantomas = toolchains.dotnet,
  gofumpt = toolchains.go,
  goimports = toolchains.go,
  golangci_lint = toolchains.go,
  ['golangci-lint'] = toolchains.go,
  gomodifytags = toolchains.go,
  impl = toolchains.go,
  java_debug_adapter = toolchains.java,
  ['java-debug-adapter'] = toolchains.java,
  java_test = toolchains.java,
  ['java-test'] = toolchains.java,
  js_debug_adapter = toolchains.node,
  ['js-debug-adapter'] = toolchains.node,
  jsonlint = toolchains.node,
  ktlint = toolchains.java,
  markdown_toc = toolchains.node,
  ['markdown-toc'] = toolchains.node,
  markdownlint_cli2 = toolchains.node,
  ['markdownlint-cli2'] = toolchains.node,
  mdformat = toolchains.python,
  netcoredbg = toolchains.dotnet,
  prettier = toolchains.node,
  rubocop = toolchains.ruby,
  yamllint = toolchains.python,
}

M.formatters = {
  csharpier = toolchains.dotnet,
  erb_format = toolchains.ruby,
  fantomas = toolchains.dotnet,
  gofumpt = toolchains.go,
  goimports = toolchains.go,
  ktlint = toolchains.java,
  markdown_toc = toolchains.node,
  ['markdown-toc'] = toolchains.node,
  markdownlint_cli2 = toolchains.node,
  ['markdownlint-cli2'] = toolchains.node,
  mdformat = toolchains.python,
  nixfmt = toolchains.nix,
  prettier = toolchains.node,
  rubocop = toolchains.ruby,
  ruff_format = toolchains.python,
  ruff_organize_imports = toolchains.python,
}

M.linters = {
  eslint = toolchains.node,
  eslint_d = toolchains.node,
  golangcilint = toolchains.go,
  jsonlint = toolchains.node,
  ktlint = toolchains.java,
  markdownlint_cli2 = toolchains.node,
  ['markdownlint-cli2'] = toolchains.node,
  rubocop = toolchains.ruby,
  statix = toolchains.nix,
  yamllint = toolchains.python,
}

function M.has_any(commands)
  for _, command in ipairs(commands) do
    if vim.fn.executable(command) == 1 then
      return true
    end
  end

  return false
end

function M.has_all(commands)
  for _, command in ipairs(commands) do
    if vim.fn.executable(command) ~= 1 then
      return false
    end
  end

  return true
end

function M.available(requirement)
  if not requirement then
    return true
  end

  if requirement.any and not M.has_any(requirement.any) then
    return false
  end

  if requirement.all and not M.has_all(requirement.all) then
    return false
  end

  return true
end

function M.filter_names(names, requirements)
  if type(names) ~= 'table' then
    return names
  end

  return vim.tbl_filter(function(name)
    return M.available(requirements[name])
  end, names)
end

function M.gate_lsp_servers(servers)
  if type(servers) ~= 'table' then
    return servers
  end

  for server, requirement in pairs(M.lsp_servers) do
    if servers[server] ~= nil and not M.available(requirement) then
      servers[server] = type(servers[server]) == 'table' and servers[server] or {}
      servers[server].enabled = false
    end
  end

  return servers
end

function M.filter_mason_tools(tools)
  return M.filter_names(tools, M.mason_tools)
end

local function filter_nested_name_lists(lists, requirements)
  if type(lists) ~= 'table' then
    return lists
  end

  for key, names in pairs(lists) do
    if type(names) == 'table' then
      lists[key] = M.filter_names(names, requirements)
    end
  end

  return lists
end

function M.filter_formatters_by_ft(formatters_by_ft)
  return filter_nested_name_lists(formatters_by_ft, M.formatters)
end

function M.filter_linters_by_ft(linters_by_ft)
  return filter_nested_name_lists(linters_by_ft, M.linters)
end

return M
