export default {
  name: "Homebrew utilities",
  type: "brew",
  packages: [
    // http://conqueringthecommandline.com/book/ack_ag
    "ack",
    "ag",
    // https://github.com/wting/autojump
    "autojump",
    // alternative to `cat`: https://github.com/sharkdp/bat
    "bat",
    // Install GNU core utilities (PATH updated automatically during install)
    "coreutils",
    "dos2unix",
    // Install GNU `find`, `locate`, `updatedb`, and `xargs`, `g`-prefixed
    "findutils",
    "fortune",
    "fzf",
    "readline", // ensure gawk gets good readline
    "gawk",
    // http://www.lcdf.org/gifsicle/ (because I'm a gif junky)
    "gifsicle",
    "gnupg",
    // Install GNU `sed` (PATH updated automatically during install)
    "gnu-sed",
    // Install GNU grep (PATH updated automatically during install)
    "grep",
    // https://github.com/jkbrzt/httpie
    "httpie",
    // jq is a sort of JSON grep
    "jq",
    // Mac App Store CLI: https://github.com/mas-cli/mas
    "mas",
    // Install some other useful utilities like `sponge`
    "moreutils",
    "nmap",
    // 'openconnect',
    "reattach-to-user-namespace",
    "ripgrep",
    // better/more recent version of screen
    "homebrew/dupes/screen",
    "tmux",
    "todo-txt",
    "tree",
    "ttyrec",
    // better, more recent vim
    "vim --with-client-server --with-override-system-vi",
    "watch",
    // Install wget with IRI support
    "wget --enable-iri",
  ],
}; 