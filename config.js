// Search for casks and formulas at https://formulae.brew.sh
module.exports = {
  brew: [
    "ack", // http://conqueringthecommandline.com/book/ack_ag
    "ag", // Code Search similar to ack (https://github.com/ggreer/the_silver_searcher)
    "autojump", //github.com/wting/autojump // https
    "bat", //github.com/sharkdp/bat, alternative to `cat`: https
    "brew-cask-completion",
    "coreutils", // Don’t forget to add `$(brew --prefix coreutils)/libexec/gnubin` to `$PATH`. Install GNU core utilities (those that come with macOS are outdated)
    "docker-completion",
    "docker-compose-completion",
    "docker-slim", // Minify and secure docker images (https://dockersl.im)
    "findutils", // Install GNU `find`, `locate`, `updatedb`, and `xargs`, `g`-prefixed
    "fzf",
    "gifsicle", // GIF animation creator/editor (https://www.lcdf.org/gifsicle/)
    "git",
    "git-credential-manager",
    "git-open",
    "gnupg",
    "graphviz",
    // Install GNU `sed`, overwriting the built-in `sed`
    // so we can do "sed -i "s/foo/bar/" file" instead of "sed -i "" "s/foo/bar/" file"
    "gnu-sed --with-default-names",
    "grep --with-default-names", // upgrade grep so we can get things like inverted match (-v)
    // better, more recent grep
    "homebrew/dupes/grep", // better, more recent grep
    "homebrew/dupes/screen", // better/more recent version of screen
    "htop",
    "httpie", // HTTP Client - powerful for API testing - alternative to Postman (https://github.com/jkbrzt/httpie | https://httpie.io/docs)
    "lazygit",
    "jq", // JSON Processor | is a sort of JSON sed / grep (https://stedolan.github.io/jq/)
    "mas", // Mac App Store CLI: https://github.com/mas-cli/mas
    "maven",
    "maven-completion",
    "moreutils", // Install some other useful utilities like `sponge` (https://joeyh.name/code/moreutils/)
    "nmap", // Network Mapper (https://nmap.org)
    "openjdk@11",
    "plantuml",
    "thefuck",
    "tree", // The Tree Command for *nix (http://mama.indstate.edu/users/ice/tree/)
    "ttyrec", // TTY Recorder and Player | Shell Macro? (http://0xcc.net/ttyrec/)
    "vim --with-client-server --with-override-system-vi", // better, more recent vim
    "watch",
    "wget --enable-iri", // Install wget with IRI support
    "yarn-completion",
  ],
  cask: [
    "aldente",
    "alfred",
    "bartender",
    // "battle-net",
    "blender",
    // "cheetah3d",
    "curiosity",
    "dash",
    "deltawalker",
    "devonagent",
    // "devonthink2",
    "drawio",
    // "docker",
    // "dotnet",
    // "electrum",
    // "epic-games",
    "firefox",
    // "ganache", // Block Chain tooling
    // "ganttproject",
    "github", // Github Desktop
    // "gswitch", // GPU Controll Software when on Intel based Macs to enforce internal / dedicated
    // "gog-galaxy",
    "handbrake",
    "iterm2",
    // "intellij-idea",
    "istat-menus",
    // "jetbrains-toolbox",
    // "ledger-live",
    "little-snitch",
    // "macpass", // Normally covered through 1password
    // "microsoft-auto-update",
    // "microsoft-edge",
    // "microsoft-office",
    "microsoft-remote-desktop",
    // "notion",
    "onedrive",
    "paragon-ntfs",
    // "parallels", // Only if really necessary
    "plasticscm-cloud-edition",
    "portfolioperformance",
    "postman",
    "rancher",
    // "resilio-sync",
    // "rider",
    // "screenflow",
    // "sevenzip", // Command Line only
    "signal",
    "slack",
    "smartgit",
    "snagit",
    // "steam",
    // "spark-ar-studio",
    "sublime-text@3",
    "thebrain",
    "turbo-boost-switcher",
    "unity-hub",
    "visual-studio-code",
    // "vlc",
    // "xquartz"
  ],
  gem: [],
  npm: [
    "eslint",
    // "generator-dockerize",
    // "gulp",
    "npm-check-updates",
    "prettyjson",
    // "trash",
    "trash-cli",
    "ts-node",
    "vtop",
    "yarn"
    // ,"yo"
  ],
  mas: [
    // 1 Password
    "1333542190",
    // Affinity Designer
    "824171161",
    // Affinity Photo
    "824183456",
    // Affinity Publisher
    "881418622",
    // AirMail 5
    "918858936",
    //com.if.Amphetamine [like caffeine]
    "937984704",
    // Day One
    "1055511498",
    // DaisyDisk
    "411643860",
    // Disk Diet
    "445512770",
    // Fantastical
    "975937182",
    // Gemini Duplicate Finder
    "1090488118",
    // Pluralsight
    "431748264",
    // Pixelmator Pro
    "1289583905",
    // Telegram
    "747648890",
    // The Unarchiver
    "425424353",
    // TickTick
    "966085870",
    // Xcode
    "497799835",
    //net.shinyfrog.bear (1.6.15)
    //"1091189122",
    //com.monosnap.monosnap (3.5.8)
    //"540348655",
    //com.app77.pwsafemac (4.17)
    //"520993579",
  ],
};
