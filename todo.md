# Dotfiles

## General

- Integrate [EditorConfig](https://editorconfig.org/)
  - Add function to replicate this to project directories easily
  - Expand it with Unity aspects or have multiple templates?
    - Likely the later as I disagree with the AirBnB styling we use at N-Dream AG due to its inherent issue with being efficient and clear.

## Brew

- Split Brewfiles into context specific setups
  - Including mas / vscode
  - Disable plugin sync on vscode?
- Node / Gem dependencies?

## NeoVIM

- Configure markdown preview from [markdown_preview.lua](./nvim/lua/plugins/markdown_preview.lua) in the markdown lsp command binding.
  - the different capabilities require either a lot of checking or distinct setups. I start to favor the later given the distinctions.
- Either complete null-ls setup for omnisharp or replace with conform.vim
- Omnisharp seems exceptionally slow, consider using csharp-lsp potentially
  (but would lose roslyn)
- Are there more hidden powers in telescope | ripgrep | fzf based solutions?
- How do I properly configure all the key lsp I have and ensure that there is
  always only one similar to the conform.vim fallthrough system?
- Further analyze [nvim-for-webdev](https://github.com/jellydn/nvim-for-webdev)
  - Add [lspsaga](https://nvimdev.github.io/lspsaga)
  - Add [alpha-vim](https://github.com/goolord/alpha-nvim)
    - lazyextras `lazyvim.plugins.extras.ui.alpha`
  - Add folding [nvim-ufo](https://github.com/kevinhwang91/nvim-ufo)
  - x
