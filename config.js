// Search for casks and formulas at https://formulae.brew.sh
module.exports = {
  brew: [
    "ack", // http://conqueringthecommandline.com/book/ack_ag
    "ag", // Code Search similar to ack (https://github.com/ggreer/the_silver_searcher)
    "agg", // converter for asciinema -> agg demo.cast demo.gif for example
    "asciinema", // ascii recorder
    "asdf",
    "autoenv",
    "autojump", // power tool to navigate: j > jump dir, jc > jump child dir, jo > open in file manager, jco > open child dir in fileman
    "asimov", // https://asimov.io/ // Ignore dev cache fragments from TimeMachine
    "bat", //github.com/sharkdp/bat, alternative to `cat`: https
    "brew-cask-completion",
    "cloudflared", // cloudflare tooling
    "cloudflare-wrangler", // wrangler for pages / worker project management
    "coreutils", // Don’t forget to add `$(brew --prefix coreutils)/libexec/gnubin` to `$PATH`. Install GNU core utilities (those that come with macOS are outdated)
    "dive", // Analysis tool for docker layers to reduce size and fix caching
    // "docker",
    // "docker-completion",
    "docker-slim", // Minify and secure docker images (https://dockersl.im)
    "eza", // exa successor, ls alternative
    "fd", // better find
    "findutils", // Install GNU `find`, `locate`, `updatedb`, and `xargs`, `g`-prefixed
    "fzf",
    "gawk", // GNU `awk`, overwriting the built-in `awk`
    "gh", // github cli for login etc
    "gifsicle", // GIF animation creator/editor (https://www.lcdf.org/gifsicle/)
    "git",
    "git-flow",
    "git-lfs",
    "git-open",
    "graphviz",
    // Install GNU `sed`, overwriting the built-in `sed`
    // so we can do "sed -i "s/foo/bar/" file" instead of "sed -i "" "s/foo/bar/" file"
    "gnu-sed --with-default-names",
    "grep --with-default-names", // upgrade grep so we can get things like inverted match (-v)
    // better, more recent grep
    "grip", // Instant github readme previewer
    // "homebrew/dupes/grep", // better, more recent grep
    "homebrew/dupes/screen", // better/more recent version of screen
    "htop",
    "httpie", // HTTP Client - powerful for API testing - alternative to Postman (https://github.com/jkbrzt/httpie | https://httpie.io/docs)
    "k9s",
    "lazydocker",
    "lazygit",
    "lazyjournal",
    "lazyjj",
    "jj",
    "jq", // JSON Processor | is a sort of JSON sed / grep (https://stedolan.github.io/jq/)
    "kubectx", // https://github.com/ahmetb/kubectx
    "kubernetes-cli", // kubectl
    "mas", // Mac epp Store CLI: https://github.com/mas-cli/mas
    "minikube",
    "moreutils", // Install some other useful utilities like `sponge` (https://joeyh.name/code/moreutils/)
    "navi", // cheatsheet for commandline
    "neovim",
    "nmap", // Network Mapper (https://nmap.org)
    "nvtop", // NVTOP stands for Neat Videocard TOP, a (h)top like task monitor for GPUs and accelerators. It can handle multiple GPUs and print information about them in a htop-familiar way.
    "opencode-ai/tap/opencode", // Alternative to Claude Code / Gemini CLI
    "podman",
    "podman-tui",
    "rg", // ripgrep
    "reattach-to-user-namespace", // required by .tmux.conf
    "sevenzip", // cli sevenzip in the form of 7zz
    "shellcheck",
    "syncthing",
    "tldr",
    "tmux",
    "tree", // The Tree Command for *nix (http://mama.indstate.edu/users/ice/tree/)
    "tree-sitter",
    "ttyrec", // TTY Recorder and Player | Shell Macro? (http://0xcc.net/ttyrec/)
    "up", // Ultimate Plumber - interactive data pipelining to work with *nix shell commands
    "uni", // Unicode database querier: uni identity $  or uni i $
    // "vim --with-client-server --with-override-system-vi", // better, more recent vim
    "watch",
    "wget --enable-iri", // Install wget with IRI support
    "yazi",
    "yq" // Process YAML, JSON, XML, CSV and properties documents from the CLI
  ],
  cask: [
    "1password",
    "1password-cli",
    "aldente",
    "alfred",
    "android-platform-tools",
    "app-tamer",
    "balenaetcher",
    "bartender",
    "bundletool",
    //"battle-net",
    "blender",
    "cheetah3d",
    "google-chrome",
    "hex-fiend",
    // "context",
    //"curiosity",
    "coteditor",
    "dash@6",
    "deltawalker",
    "devonagent",
    // "devonthink2",
    "drawio",
    // "docker", // to be replaced with podman
    // "dotnet",
    // "electrum",
    "epic-games",
    // "firefox",
    "font-jetbrains-mono-nerd-font",
    "fork",
    // "ganache", // Block Chain tooling
    "ganttproject",
    "gitup-app",
    "github", // Github Desktop
    //"gswitch", // GPU Controll Software when on Intel based Macs to enforce internal / dedicated
    "gog-galaxy",
    "ghostty",
    "handbrake-app",
    "istat-menus",
    // "iterm2",
    "jetbrains-toolbox",
    // "kitty", // replaced with ghostty
    //"latest",
    "ledger-live",
    "little-snitch",
    "lm-studio",
    "ghostty",
    // "micropip install trash-clisoft-auto-update",
    "microsoft-edge",
    "microsoft-office",
    "obsidian",
    "ollama-app",
    "paragon-ntfs",
    "parallels", // Only if really necessary
    "plasticscm-cloud-edition",
    "podman-desktop",
    "portfolioperformance",
    "rectangle",
    // "resilio-sync",
    "screenflow",
    "signal",
    "slack",
    "snagit",
    "steam",
    "syncthing-app",
    //"spark-ar-studio",
    //"sublime-text",
    "thebrain",
    "thonny",
    "unity-hub",
    "visual-studio-code",
    "vlc",
    // "xquartz"
  ],
  gem: [
    "rake"
  ],
  npm: [ // I would really like to have I and U operations and context|environment to split private/work
    // "eslint",
    // "generator-dockerize",
    // "gulp",
    "npm-check-updates", // ncu in shell afterwards
    "openupm-cli",
    "prettyjson",
    "vtop",
    // "yarn",
    // ,"yo"
  ],
  mas: [
    // 1 Password
    "1333542190",
    // Assassin's Creed Mirage
    "6472704261",
    // Affinity Designer 2
    "1616831348",
    // Affinity Photo 2
    "1616822987",
    // Affinity Publisher 2
    "1606941598",
    //com.if.Amphetamine [like caffeine]
    "937984704",
    // Day One
    "1055511498",
    // DaisyDisk
    "411643860",
    // Hugging Face Diffusers
    "1666309574",
    // Disk Diet
    "445512770",
    // Gemini Duplicate Finder
    "1090488118",
    // Good Notes 5+
    "1444383602",
    // iA Writer
    "775737590",
    // Kindle
    "302584613",
    // Magnet
    // "441258766", // replaced by cask rectangle
    // Money
    "1185488696",
    // Pluralsight
    "431748264",
    // Pixelmator Pro
    "1289583905",
    // Scapple
    "568020055",
    // Spark
    "6472704261",
    // Stoic
    "1312926037",
    // Telegram
    "747648890",
    // toggl-track-hours-time-log
    "1291898086",
    // The Unarchiver
    "425424353",
    // TickTick
    "966085870",
    // Time Sink
    "404363161",
    // Vinegar
    "1591303229",
    // Whatsapp
    "310633997",
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
