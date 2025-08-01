export default {
  brew: [
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
  cask: [
    //'adium',
    //'amazon-cloud-drive',
    //'atom',
    //'box-sync',
    //'comicbooklover',
    //'diffmerge',
    "docker", // docker for mac
    //'dropbox',
    //'evernote',
    //'flux',
    // "gpg-suite",
    //'ireadfast',
    "iterm2",
    //'little-snitch',
    // 'macbreakz',
    //'micro-snitch',
    // 'signal',
    //'macvim',
    // "sizeup",
    //'sketchup',
    "slack",
    // 'the-unarchiver',
    //'torbrowser',
    //'transmission',
    "visual-studio-code",
    //'vlc',
    // "xquartz",
  ],
  gem: [],
  npm: [
    "@anthropic-ai/claude-code",
    "antic",
    "buzzphrase",
    "eslint",
    "@google/gemini-cli",
    "trash",
    "vtop",
  ],
  mas: [
    //com.apple.dt.Xcode (10.2.1)
    //"497799835",
    //com.if.Amphetamine (4.1.6)
    //'937984704',
    //net.shinyfrog.bear (1.6.15)
    //'1091189122',
    //com.monosnap.monosnap (3.5.8)
    //'540348655',
    //com.app77.pwsafemac (4.17)
    //'520993579',
  ],
};
