const emoji = require('node-emoji')
const fs = require('fs')
const inquirer = require('inquirer')
const config = require('./config')
const command = require('./lib_node/command')

inquirer.prompt([{
  type: 'confirm',
  name: 'gitshots',
  message: 'Do you want to use gitshots?',
  default: true
}]).then(function (answers) {
  if(answers.gitshots){

    // additional brew packages needed to support gitshots
    config.brew.push('imagemagick', 'imagesnap')
    // ensure ~/.gitshots exists
    command('mkdir -p ~/.gitshots', __dirname, function(err, out) {
      if(err) throw err
    })
    // add post-commit hook
    command('cp ./.git_template/hooks/gitshot-pc ./.git_template/hooks/post-commit', __dirname, function(err, out) {
      if(err) throw err
    })
  }else{
    if(fs.existsSync('./.git_template/hooks/post-commit')){
      // disable post-commit (in case we are undoing the git-shots enable)
      // TODO: examine and remove/comment out the file content with the git shots bit
      command('mv ./.git_template/hooks/post-commit ./.git_template/hooks/disabled-pc', __dirname, function(err, out) {
        if(err) throw err
      })
    }
  }

  const installPackages = function(type){
    console.info(emoji.get('coffee'), ' installing '+type+' packages')
    config[type].map(function(item){
      console.info(type+':', item)
      command('. lib_sh/echos.sh && . lib_sh/requirers.sh && require_'+type+' ' + item, __dirname, function(err, out) {
        if(err) console.error(emoji.get('fire'), err)
      })
    })
  }

  installPackages('brew')
  installPackages('cask')
  installPackages('npm')
  installPackages('gem')

})
