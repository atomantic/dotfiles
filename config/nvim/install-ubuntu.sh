# Install ??

# Add repositories
sudo add-apt-repository ppa:deadsnakes/ppa
sudo add-apt-repository ppa:neovim-ppa/unstable -y
sudo apt update

# Install Python
sudo apt install python3.10 python3.10-venv

# Install Neovim 0.8+
sudo apt install neovim

sudo apt install lua5.4 ripgrep unzip clang

wget -qO- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.0/install.sh | bash
source ~/.bashrc
nvm install --lts

LAZYGIT_VERSION=$(curl -s "https://api.github.com/repos/jesseduffield/lazygit/releases/latest" | grep -Po '"tag_name": "v\K[0-9.]+')
curl -Lo lazygit.tar.gz "https://github.com/jesseduffield/lazygit/releases/latest/download/lazygit_${LAZYGIT_VERSION}_Linux_x86_64.tar.gz"
sudo tar xf lazygit.tar.gz -C /usr/local/bin lazygit
lazygit --version

# Open NVIM and test - use :MasonToolsUpdateSync to full test it
nvim .
