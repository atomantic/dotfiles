return {
  'nvim-treesitter/nvim-treesitter',
  build = ':TSUpdate',
  -- event = { 'LazyFile', 'VeryLazy' },
  -- lazy = vim.fn.argc(-1) == 0, -- load treesitter early when opening a file from the cmdline
  -- init = function(plugin)
  --[[
    PERF: add nvim-treesitter queries to the rtp and it's custom query predicates early
    This is needed because a bunch of plugins no longer `require("nvim-treesitter")`, which
    no longer trigger the **nvim-treesitter** module to be loaded in time.
    Luckily, the only things that those plugins need are the custom queries, which we make available
    during startup. 
    --]]
  --   require('lazy.core.loader').add_to_rtp(plugin)
  --   require('nvim-treesitter.query_predicates')
  -- end,
  opts = {
    indent = { enable = true },
    -- A list of parser names, or "all" (the listed parsers MUST always be installed)
    ensure_installed = {
      'lua',
      'vim',
      'vimdoc',
      'csv',
      'regex',
      'sql',
      'terraform',
      'tmux',
      'typescript',
      'vue',
      'yaml',
      'xml',
      'jq',
      'python',
      'javascript',
      'html',
      'css',
      'diff',
      'gitattributes',
      'gitcommit',
      'http',
      'json',
      'luadoc',
      'nix',
      'query',
      'markdown',
      'markdown_inline',
    },

    -- Install parsers synchronously (only applied to `ensure_installed`)
    sync_install = true,

    -- Automatically install missing parsers when entering buffer
    -- Recommendation: set to false if you don't have `tree-sitter` CLI installed locally
    auto_install = true,

    -- List of parsers to ignore installing (or "all")
    -- ignore_install = { 'javascript' },

    ---- If you need to change the installation directory of the parsers (see -> Advanced Setup)
    -- parser_install_dir = "/some/path/to/store/parsers", -- Remember to run vim.opt.runtimepath:append("/some/path/to/store/parsers")!

    highlight = {
      enable = true,

      -- NOTE: these are the names of the parsers and not the filetype. (for example if you want to
      -- disable highlighting for the `tex` filetype, you need to include `latex` in this list as this is
      -- the name of the parser)
      -- list of language that will be disabled
      -- disable = { 'c', 'rust' },
      -- Or use a function for more flexibility, e.g. to disable slow treesitter highlight for large files
      -- disable = function(lang, buf)
      --   local max_filesize = 100 * 1024 -- 100 KB
      --   local ok, stats = pcall(vim.loop.fs_stat, vim.api.nvim_buf_get_name(buf))
      --   if ok and stats and stats.size > max_filesize then
      --     return true
      --   end
      -- end,

      -- Setting this to true will run `:h syntax` and tree-sitter at the same time.
      -- Set this to `true` if you depend on 'syntax' being enabled (like for indentation).
      -- Using this option may slow down your editor, and you may see some duplicate highlights.
      -- Instead of true it can also be a list of languages
      -- additional_vim_regex_highlighting = false,
    },
  },
  config = function(_, opts)
    if type(opts.ensure_installed) == 'table' then
      opts.ensure_installed = LazyVim.dedup(opts.ensure_installed)
    end
    require('nvim-treesitter.configs').setup(opts)
  end,
}
