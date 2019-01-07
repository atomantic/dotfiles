module.exports = {
  brew: [
    // http://conqueringthecommandline.com/book/ack_ag
    'ack',
    'ag',
    // alternative to `cat`: https://github.com/sharkdp/bat
    'bat',
      // Install GNU core utilities (those that come with macOS are outdated)
      'cowsay',
      // Update and install github
      'git',
      // cmake is required to compile vim bundle YouCompleteMe
      'cmake',
      // Install GNU core utilities (those that come with OS X are outdated)
      // Don’t forget to add `$(brew --prefix coreutils)/libexec/gnubin` to `$PATH`.
      'coreutils',
      'dos2unix',
      // Install GNU `find`, `locate`, `updatedb`, and `xargs`, `g`-prefixed
      'findutils',
      // 'fortune',
      'fzf',
      'readline', // ensure gawk gets good readline
      'freetype',
      'gawk',
      // http://www.lcdf.org/gifsicle/ (because I'm a gif junky)
      'gifsicle',
      'gnupg',
      // Install GNU `sed`, overwriting the built-in `sed`
      // so we can do "sed -i 's/foo/bar/' file" instead of "sed -i '' 's/foo/bar/' file"
      'gnu-sed --with-default-names',
      // hub - https://hub.github.com/
      'hub',
      // upgrade grep so we can get things like inverted match (-v)
      'grep --with-default-names',
      // better, more recent grep
      'homebrew/dupes/grep',
      // https://github.com/jkbrzt/httpie
      'httpie',
      'imagemagick',
      'imagesnap',
      // jq is a sort of JSON grep
      'jq',
      // Mac App Store CLI: https://github.com/mas-cli/mas
      'mas',
      // Markdown mode
      'markdown',
      // Install some other useful utilities like `sponge`
      'moreutils',
      'nmap',
      'openssl',
      'openconnect',
      'pandoc',
      'reattach-to-user-namespace',
      // better/more recent version of screen
      'homebrew/dupes/screen',
      'tmux',
      'todo-txt',
      'tree',
      'ttyrec',
      // create gif of terminal sessions
      'ttygif',
      // better, more recent vim
      'vim --with-override-system-vi',
      'watch',
      // Install wget with IRI support
      'wget --enable-iri',
      // Watch & Manage System Resources
      'htop-osx',
      // Command line file manager
      'midnight-commander',
      // R statistical software
      'r',
      // zsh package manager
      'zplug'
  ],
    cask: [
        //'adium',
        //'amazon-cloud-drive',
        'anaconda',
        'atom',
        'box-sync',
        //'comicbooklover',
        //'diffmerge',
        'docker',
        'djview',
        'dropbox',
        //'evernote',
        'flux',
        'gpg-suite',
        //'ireadfast',
        'iterm2',
        //'little-snitch',
        //'micro-snitch',
        'BetterTouchTool',
        'jdownloader',
        'firefox',
        'flux',
        'mactex',
        'google-chrome',
        'google-drive',
        'spotify',
        'bettertouchtool',
        'rstudio',
        'little-snitch',
        'macbreakz',
        'micro-snitch',
        'signal',
        //'macvim',
        'sizeup',
        'skype',
        //'sketchup',
        'slack',
        'the-unarchiver',
        //'torbrowser',
        //'transmission',
        'vlc',
        'visual-studio-code',
        'xquartz'
    ],
    gem: [
    ],
    npm: [
        'antic',
        'buzzphrase',
        'eslint',
        'instant-markdown-d',
        'npm-check',
        'yo',
        'generator-dockerize',
        'gulp',
        // 'generator-dockerize',
        // 'gulp',
        'npm-check-updates',
        'prettyjson',
        'trash',
        'vtop'
    ]
};
