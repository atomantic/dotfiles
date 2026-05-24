-- Fix: refactoring.nvim now requires lewis6991/async.nvim but LazyVim's extras
-- spec doesn't declare it yet. Override to add the missing dependency.
return {
  'ThePrimeagen/refactoring.nvim',
  dependencies = {
    'lewis6991/async.nvim',
  },
}
