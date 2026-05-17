-- Handled by LSP Zero in the current case, but this might be relevant to take it over explicitely
-- Originally from github.com/cpow/neovim-for-newbs/
if true then
  return {}
end

return {
  'nvimtools/none-ls.nvim',
  config = function()
    local null_ls = require('null-ls')
    null_ls.setup({
      sources = {
        null_ls.builtins.formatting.stylua,
        null_ls.builtins.formatting.prettier,
        null_ls.builtins.formatting.eslint,
        -- null_ls.builtins.diagnostics.erb_lint,
        -- null_ls.builtins.diagnostics.rubocop,
        -- null_ls.builtins.formatting.rubocop
      },
    })

    vim.keymap.set('n', '<leader>gf', vim.lsp.buf.format, {})
  end,
}
