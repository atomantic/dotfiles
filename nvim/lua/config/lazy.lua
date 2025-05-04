local lazypath = vim.fn.stdpath("data") .. "/lazy/lazy.nvim"
if not (vim.uv or vim.loop).fs_stat(lazypath) then
  local lazyrepo = "https://github.com/folke/lazy.nvim.git"
  local out = vim.fn.system({ "git", "clone", "--filter=blob:none", "--branch=stable", lazyrepo, lazypath })
  if vim.v.shell_error ~= 0 then
    vim.api.nvim_echo({
      { "Failed to clone lazy.nvim:\n", "ErrorMsg" },
      { out, "WarningMsg" },
      { "\nPress any key to exit..." },
    }, true, {})
    vim.fn.getchar()
    os.exit(1)
  end
end
vim.opt.rtp:prepend(lazypath)

require("lazy").setup({
  spec = {
    -- add LazyVim and import its plugins
    { "LazyVim/LazyVim", import = "lazyvim.plugins" },
    -- import/override with your plugins
    { import = "plugins" },
  },
  defaults = {
    -- By default, only LazyVim plugins will be lazy-loaded. Your custom plugins will load during startup.
    -- If you know what you're doing, you can set this to `true` to have all your custom plugins lazy-loaded by default.
    lazy = false,
    -- It's recommended to leave version=false for now, since a lot the plugin that support versioning,
    -- have outdated releases, which may break your Neovim install.
    version = false, -- always use the latest git commit
    -- version = "*", -- try installing the latest stable version for plugins that support semver
  },
  install = { colorscheme = { "tokyonight", "habamax" } },
  checker = {
    enabled = true, -- check for plugin updates periodically
    notify = false, -- notify on update
  }, -- automatically check for plugin updates
  performance = {
    rtp = {
      -- disable some rtp plugins
      disabled_plugins = {
        "gzip",
        -- "matchit",
        -- "matchparen",
        -- "netrwPlugin",
        "tarPlugin",
        "tohtml",
        "tutor",
        "zipPlugin",
      },
    },
  },
})

-- require("telescope").setup({
--   extensions = {
--     media_files = {
--       -- filetypes whitelist
--       -- defaults to {"png", "jpg", "mp4", "webm", "pdf"}
--       filetypes = { "png", "webp", "jpg", "jpeg", "pdf" },
--       -- find command (defaults to `fd`)
--       find_cmd = "rg",
--     },
--   },
-- })

-- require("neo-tree").setup({
--   filesystem = {
--     window = {
--       mappings = {
--         ["<C-i>"] = "image_kitty", -- " or another map
--       },
--     },
--     commands = {
--       image_kitty = function(state)
--         local node = state.tree:get_node()
--         if node.type == "file" then
--           require("image_preview").PreviewImage(node.path)
--         end
--       end,
--     },
--   },
-- })

--[[
local lsp_zero = require("lsp-zero")

lsp_zero.on_attach(function(client, bufnr)
  -- see :help lsp-zero-keybindings-- to learn the available actions
  lsp_zero.default_keymaps({ buffer = bufnr })
end)

-- here you can setup the language servers
require("lspconfig").tsserver.setup({})
require("lspconfig").tailwindcss.setup({})
--]]
