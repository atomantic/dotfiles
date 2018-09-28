

  
  if &background == 'dark'
    
  let s:shade0 = "#282c34"
  let s:shade1 = "#393e48"
  let s:shade2 = "#4b515c"
  let s:shade3 = "#5c6370"
  let s:shade4 = "#636d83"
  let s:shade5 = "#828997"
  let s:shade6 = "#979eab"
  let s:shade7 = "#abb2bf"
  let s:accent0 = "#e06c75"
  let s:accent1 = "#d19a66"
  let s:accent2 = "#e5c07b"
  let s:accent3 = "#98c379"
  let s:accent4 = "#56b6c2"
  let s:accent5 = "#61afef"
  let s:accent6 = "#c678dd"
  let s:accent7 = "#be5046"
  
  endif
  

  
  if &background == 'light'
    
  let s:shade0 = "#fafafa"
  let s:shade1 = "#cdced1"
  let s:shade2 = "#a0a1a7"
  let s:shade3 = "#9d9d9f"
  let s:shade4 = "#83858b"
  let s:shade5 = "#696c77"
  let s:shade6 = "#51535d"
  let s:shade7 = "#383a42"
  let s:accent0 = "#e45649"
  let s:accent1 = "#986801"
  let s:accent2 = "#c18401"
  let s:accent3 = "#50a14f"
  let s:accent4 = "#0184bc"
  let s:accent5 = "#4078f2"
  let s:accent6 = "#a626a4"
  let s:accent7 = "#ca1243"
  
  endif
  

  let s:p = {'normal': {}, 'inactive': {}, 'insert': {}, 'replace': {}, 'visual': {}, 'tabline': {}}
  let s:p.normal.left = [ [ s:shade1, s:accent5 ], [ s:shade7, s:shade2 ] ]
  let s:p.normal.right = [ [ s:shade1, s:shade4 ], [ s:shade5, s:shade2 ] ]
  let s:p.inactive.right = [ [ s:shade1, s:shade3 ], [ s:shade3, s:shade1 ] ]
  let s:p.inactive.left =  [ [ s:shade4, s:shade1 ], [ s:shade3, s:shade0 ] ]
  let s:p.insert.left = [ [ s:shade1, s:accent3 ], [ s:shade7, s:shade2 ] ]
  let s:p.replace.left = [ [ s:shade1, s:accent1 ], [ s:shade7, s:shade2 ] ]
  let s:p.visual.left = [ [ s:shade1, s:accent6 ], [ s:shade7, s:shade2 ] ]
  let s:p.normal.middle = [ [ s:shade5, s:shade1 ] ]
  let s:p.inactive.middle = [ [ s:shade4, s:shade1 ] ]
  let s:p.tabline.left = [ [ s:shade6, s:shade2 ] ]
  let s:p.tabline.tabsel = [ [ s:shade6, s:shade0 ] ]
  let s:p.tabline.middle = [ [ s:shade2, s:shade4 ] ]
  let s:p.tabline.right = copy(s:p.normal.right)
  let s:p.normal.error = [ [ s:accent0, s:shade0 ] ]
  let s:p.normal.warning = [ [ s:accent2, s:shade1 ] ]

  let g:lightline#colorscheme#ThemerVimLightline#palette = lightline#colorscheme#fill(s:p)

  