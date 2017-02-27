module.exports = {
  brew: [
    'ansible',
    'awscli',
    'aws-elasticbeanstalk',
    'lame',
    'pwgen',
    'ssh-copy-id',
    'terminal-notifier',
    // http://conqueringthecommandline.com/book/ack_ag
    'ack',
    'ag',
    // Install GNU core utilities (those that come with OS X are outdated)
    // Don’t forget to add `$(brew --prefix coreutils)/libexec/gnubin` to `$PATH`.
    'coreutils',
    'dos2unix',
    // Install GNU `find`, `locate`, `updatedb`, and `xargs`, `g`-prefixed
    'findutils',
    'fortune',
    'gawk',
    // http://www.lcdf.org/gifsicle/ (because I'm a gif junky)
    'gifsicle',
    'gnupg',
    // Install GNU `sed`, overwriting the built-in `sed`
    // so we can do "sed -i 's/foo/bar/' file" instead of "sed -i '' 's/foo/bar/' file"
    'gnu-sed --with-default-names',
    // better, more recent grep
    'homebrew/dupes/grep',
    // https://github.com/jkbrzt/httpie
    'httpie',
    // jq is a sort of JSON grep
    'jq',
    // Mac App Store CLI: https://github.com/mas-cli/mas
    'mas',
    // Install some other useful utilities like `sponge`
    'moreutils',
    'nmap',
    'openconnect',
    'reattach-to-user-namespace',
    // better/more recent version of screen
    'homebrew/dupes/screen',
    'tree',
    'ttyrec',
    // better, more recent vim
    'vim --with-override-system-vi',
    'watch',
    // Install wget with IRI support
    'wget --enable-iri'
  ],
  cask: [
    //'adium',
    //'amazon-cloud-drive',
    'alfred',
    'amethyst',
    'atom',
    //'box-sync',
    //'comicbooklover',
    'cyberduck',
    'firefox'
    //'diffmerge',
    //'dropbox',
    //'evernote',
    'gpgtools',
    'google-chrome',
    'handbrake',
    //'ireadfast',
    'iterm2',
    //'little-snitch',
    //'micro-snitch',
    //'macvim',
    'osxfuse',
    'qlstephen',
    //'sizeup',
    'sequel-pro',
    'slack',
    'skyfonts',
    //'sketchup',
    'the-unarchiver',
    'torbrowser',
    'transmission',
    'vlc',
    'virtualbox',
    'vagrant'
    //'xquartz'
  ],
  gem: [
    'git-up'
  ],
  npm: [
    'antic',
    'buzzphrase',
    'bower',
    'cordova',
    'eslint',
    'instant-markdown-d',
    'npm-check',
    'yo',
    'foundation-cli',
    'generator-dockerize',
    'gulp',
    'grunt',
    'prettyjson',
    'trash',
    'vtop'
    // ,'yo'
  ]
};
