return {
  {
    "nvim-lualine/lualine.nvim",
    event = "VeryLazy",
    opts = {
      options = {
        theme = "powerline",
        icons_enabled = true,
        section_separators = { left = "", right = "" },
        component_separators = { left = "", right = "" },
        globalstatus = true,
        disabled_filetypes = { "dashboard", "NvimTree", "Outline" },
      },
      sections = {
        lualine_a = { { "mode", icon = "" } },
        lualine_b = { { "branch", icon = "" }, "diff", "diagnostics" },
        lualine_c = {
          { "filename", file_status = true, path = 1, icon = "📝" },
          { function() return vim.fn.getcwd() end, icon = "", color = { fg = "#fabd2f", gui = "bold" } },
        },
        lualine_x = {
          { "filetype", colored = true, icon_only = false },
          "encoding",
          "fileformat",
        },
        lualine_y = { { function() return os.date('%H:%M') end, icon = "" } },
        lualine_z = { { "location", icon = "" }, "progress" },
      },
      inactive_sections = {
        lualine_a = {},
        lualine_b = {},
        lualine_c = { { "filename", file_status = true, path = 1 } },
        lualine_x = { "location" },
        lualine_y = {},
        lualine_z = {}
      },
      extensions = { "quickfix", "fugitive", "nvim-tree" },
    },
  },
}
