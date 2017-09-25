"""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Must Have
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
colorscheme solarized
" syntax on " syntax highlighting on
syntax enable
let g:solarized_termtrans = 1
call togglebg#map("<F5>")
if has('gui_running')
    set background=light
else
    set background=dark
endif

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Vundle
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
set nocompatible              " be iMproved, required
filetype off                  " required

" set the runtime path to include Vundle and initialize
set rtp+=~/.vim/bundle/Vundle.vim
call vundle#begin()

" Keep Plugin commands between vundle#begin/end.
" let Vundle manage Vundle
Plugin 'VundleVim/Vundle.vim'
" Plugin 'Valloric/YouCompleteMe'
" Navigation (IDE frame)
Plugin 'scrooloose/nerdtree'
Plugin 'jistr/vim-nerdtree-tabs'
Plugin 'vim-airline/vim-airline'
Plugin 'vim-airline/vim-airline-themes'
Plugin 'tpope/vim-fugitive'
Plugin 'tpope/vim-sensible'
Plugin 'justinmk/vim-sneak'
Plugin 'airblade/vim-gitgutter'
Plugin 'tpope/vim-surround'
Plugin 'dkprice/vim-easygrep'
Plugin 'editorconfig/editorconfig-vim'
" visual undo list
Plugin 'sjl/gundo.vim'
" Plugin 'majutsushi/tagbar'
" markdown preview: opens browser with live reload when vim opens .md
Plugin 'suan/vim-instant-markdown'
Plugin 'godlygeek/tabular'
" language tools
Plugin 'scrooloose/syntastic'
Plugin 'millermedeiros/vim-esformatter'
Plugin 'digitaltoad/vim-pug'
" Plugin 'elzr/vim-json'
" Plugin 'SirVer/ultisnips'
"Plugin 'sheerun/vim-polyglot'
" plugins from http://vim-scripts.org/vim/scripts.html
Plugin 'node.js'
Plugin 'SuperTab'
" Git plugin not hosted on GitHub
" Plugin 'git://git.wincent.com/command-t.git'
" git repos on your local machine (i.e. when working on your own plugin)
" Plugin 'file:///home/gmarik/path/to/plugin'
" The sparkup vim script is in a subdirectory of this repo called vim.
" Pass the path to set the runtimepath properly.
" Plugin 'rstacruz/sparkup', {'rtp': 'vim/'}
" Install L9 and avoid a Naming conflict if you've already installed a
" different version somewhere else.
" Plugin 'ascenator/L9', {'name': 'newL9'}
" TypeScript
Plugin 'leafgarland/typescript-vim'
" Vue.js
Plugin 'posva/vim-vue'

" All of your Plugins must be added before the following line
call vundle#end()            " required
filetype plugin indent on    " required
" To ignore plugin indent changes, instead use:
"filetype plugin on
"
" Brief help
" :PluginList       - lists configured plugins
" :PluginInstall    - installs plugins; append `!` to update or just :PluginUpdate
" :PluginSearch foo - searches for foo; append `!` to refresh local cache
" :PluginClean      - confirms removal of unused plugins; append `!` to auto-approve removal
"
" see :h vundle for more details or wiki for FAQ
" Put your non-Plugin stuff after this line
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" General
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" set UTF-8 encoding
set enc=utf-8
set fenc=utf-8
set termencoding=utf-8
set history=1000 " How many lines of history to remember
set cf " enable error files and error jumping
" set clipboard+=unnamed " turns out I do like sharing windows clipboard
set ffs=unix,dos,mac " support all three, in this order
set viminfo+=! " make sure it can save viminfo
set isk+=_,$,@,%,# " none of these should be word dividers, so make them not be
set nosol " leave my cursor where it was

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Files/Backups/Sessions
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
set nobackup
set nowb
set noswapfile
set directory=~/.vim/temp " directory for temp files
set makeef=error.err " When using make, where should it dump the file
set sessionoptions+=globals " What should be saved during sessions being saved
set sessionoptions+=localoptions " What should be saved during sessions being saved
set sessionoptions+=resize " What should be saved during sessions being saved
set sessionoptions+=winpos " What should be saved during sessions being saved

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Vim UI
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
set lsp=0 " space it out a little more (easier to read)
set wildmenu " turn on wild menu
set wildmode=list:longest " turn on wild menu in special format (long format)
set wildignore=*.dll,*.o,*.obj,*.bak,*.exe,*.pyc,*.swp,*.jpg,*.gif,*.png " ignore formats
set ruler " Always show current positions along the bottom
set cmdheight=1 " the command bar is 1 high
set number " turn on line numbers
set lz " do not redraw while running macros (much faster) (LazyRedraw)
set hid " you can change buffer without saving
set backspace=2 " make backspace work normal
set whichwrap+=<,>,h,l  " backspace and cursor keys wrap to
set mouse=a " use mouse everywhere
set shortmess=atI " shortens messages to avoid 'press a key' prompt
set report=0 " tell us when anything is changed via :...
set noerrorbells " don't make noise
set list " we do what to show tabs, to ensure we get them out of my files
set listchars=tab:>-,trail:- " show tabs and trailing whitespace

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Visual Cues
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
set showmatch " show matching brackets
set mat=5 " how many tenths of a second to blink matching brackets for
set nohlsearch " do not highlight searched for phrases
set incsearch " BUT do highlight as you type you search phrase
set so=5 " Keep 5 lines (top/bottom) for scope
set novisualbell " don't blink
" statusline example: ~\myfile[+] [FORMAT=format] [TYPE=type] [ASCII=000] [HEX=00] [POS=0000,0000][00%] [LEN=000]
set statusline=%F%m%r%h%w\ [FORMAT=%{&ff}]\ [TYPE=%Y]\ [ASCII=\%03.3b]\ [HEX=\%02.2B]\ [POS=%04l,%04v][%p%%]\ [LEN=%L]
set laststatus=2 " always show the status line

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Indent Related
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
set ai " autoindent (filetype indenting instead)
set nosi " smartindent (filetype indenting instead)
set cindent " do c-style indenting
set softtabstop=4 " unify
set shiftwidth=4 " unify
set tabstop=4 " real tabs should be 4, but they will show with set list on
set copyindent " but above all -- follow the conventions laid before us
" wrap lines at 120 chars. 80 is somewhat antiquated with nowadays displays.
set textwidth=120
filetype plugin indent on " load filetype plugins and indent settings

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Text Formatting/Layout
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
set fo=tcrq " See Help (complex)
set shiftround " when at 3 spaces, and I hit > ... go to 4, not 5
set expandtab " no real tabs!
set nowrap " do not wrap line
set preserveindent " but above all -- follow the conventions laid before us
set ignorecase " case insensitive by default
set smartcase " if there are caps, go case-sensitive
set completeopt=menu,longest,preview " improve the way autocomplete works
set cursorcolumn " show the current column
set cursorline
" hi CursorLine term=underline ctermbg=008 guibg=#493a35

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Folding
"    Enable folding, but by default make it act like folding is
"    off, because folding is annoying in anything but a few rare
"    cases
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
set foldenable " Turn on folding
set foldmarker={,} " Fold C style code
set foldmethod=marker " Fold on the marker
set foldlevel=100 " Don't autofold anything (but I can still fold manually)
set foldopen-=search " don't open folds when you search into them
set foldopen-=undo " don't open folds when you undo stuff

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" CTags
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
"let Tlist_Ctags_Cmd = 'ctags' " Location of ctags
"let Tlist_Sort_Type = "name" " order by
"let Tlist_Use_Right_Window = 1 " split to the right side of the screen
"let Tlist_Compact_Format = 1 " show small meny
"let Tlist_Exist_OnlyWindow = 1 " if you are the last, kill yourself
"let Tlist_File_Fold_Auto_Close = 0 " Do not close tags for other files
"let Tlist_Enable_Fold_Column = 1 " Do show folding tree
"let Tlist_WinWidth = 50 " 50 cols wide, so I can (almost always) read my functions

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Matchit
"""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
let b:match_ignorecase = 1

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Perl
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" let perl_extended_vars=1 " highlight advanced perl vars inside strings

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Custom Functions
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Select range, then hit :SuperRetab($width) - by p0g and FallingCow
function! SuperRetab(width) range
    silent! exe a:firstline . ',' . a:lastline . 's/\v%(^ *)@<= {'. a:width .'}/\t/g'
endfunction

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Mappings
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" map <up> <ESC>:bp<RETURN> " left arrow (normal mode) switches buffers
" map <down> <ESC>:bn<RETURN> " right arrow (normal mode) switches buffers
" map <right> <ESC>:Tlist<RETURN> " show taglist
" map <left> <ESC>:NERDTreeToggle<RETURN>  " moves left fa split
" map <F2> <ESC>ggVG:call SuperRetab()<left>
" map <F12> ggVGg? " apply rot13 for people snooping over shoulder, good fun

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Useful abbrevs
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
iab xdate <c-r>=strftime("%d/%m/%y %H:%M:%S")<cr>

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Autocommands
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
au BufRead,BufNewFile *.zcml set filetype=xml
au BufRead,BufNewFile *.rb,*.rhtml set tabstop=2
au BufRead,BufNewFile *.rb,*.rhtml set shiftwidth=2
au BufRead,BufNewFile *.rb,*.rhtml set softtabstop=2
au BufRead,BufNewFile *.otl set syntax=blockhl
au BufRead,BufNewFile *.json set syntax=javascript
au FileType python set omnifunc=pythoncomplete#Complete
au FileType javascript set omnifunc=javascriptcomplete#CompleteJS
au FileType html set omnifunc=htmlcomplete#CompleteTags
au FileType css set omnifunc=csscomplete#CompleteCSS
au FileType xml set omnifunc=xmlcomplete#CompleteTags
au FileType c set omnifunc=ccomplete#Complete
" autocmd vimenter * NERDTree

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Change paging overlap amount from 2 to 5 (+3)
" if you swapped C-y and C-e, and set them to 2, it would
" remove any overlap between pages
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
nnoremap <C-b> <C-b>3<C-e> "  Make overlap 3 extra on control-b

" Yank text to the macOS clipboard
noremap <leader>y "*y
noremap <leader>yy "*Y

" Preserve indentation while pasting text from the macOS clipboard
noremap <leader>p :set paste<CR>:put  *<CR>:set nopaste<CR>

" esformatter
" type \es to format
nnoremap <silent> <leader>es :Esformatter<CR>
vnoremap <silent> <leader>es :EsformatterVisual<CR>

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" NERDTree
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
let NERDTreeShowHidden=1
let NERDTreeIgnore=['\.DS_Store$']
" auto open if no file sent as arg
autocmd StdinReadPre * let s:std_in=1
autocmd VimEnter * if argc() == 0 && !exists("s:std_in") | NERDTree | endif
" Toggle NERDtree with C-n
map ,n <plug>NERDTreeTabsToggle<CR>
" Autoclose if only NERDtree is left
autocmd bufenter * if (winnr("$") == 1 && exists("b:NERDTreeType") && b:NERDTreeType == "primary") | q | endif

""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Syntastic
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
set statusline+=%#warningmsg#
set statusline+=%{SyntasticStatuslineFlag()}
set statusline+=%*

let g:syntastic_html_tidy_quiet_messages = { "level": "warnings" }
let g:syntastic_html_tidy_ignore_errors = [ '<template> is not recognized!' ]

let g:syntastic_always_populate_loc_list = 1
let g:syntastic_auto_loc_list = 1
let g:syntastic_check_on_open = 1
"let g:syntastic_check_on_wq = 0
let g:syntastic_enable_eslint_checker = 1
let g:syntastic_javascript_checkers = ['eslint']
let g:syntastic_enable_tslint_checker = 1
let g:syntastic_typescript_checkers = ['tslint', 'tsc']
let g:syntastic_enable_pug_checker = 1
let g:syntastic_pug_checkers = ['jade','pug']
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
" Other
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
let g:sneak#streak = 1
let g:airline_theme='bubblegum'
