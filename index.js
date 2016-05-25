const emoji = require('node-emoji')
const inquirer = require('inquirer')
const config = require('./config')
const command = require('./lib_node/command')
const log = require('npmlog')

inquirer.prompt([{
  type: 'confirm',
  name: 'gitshots',
  message: 'Do you want to use gitshots?',
  default: true
}]).then(function (answers) {
  if(answers.gitshots){
    // ensure ~/.gitshots exists
    command('mkdir -p ~/.gitshots', __dirname, function(err, out) {
      if(err) log.error(err)
    });
  }

  // BREW
  log.info('installing brews', emoji.get('coffee'))
  config.brew.map(function(item){
    log.info('brew install', item);
    command('. lib_sh/echos.sh && . lib_sh/requirers.sh && require_brew ' + item, __dirname, function(err, out) {
      if(err) log.error(emoji.get('fire'), err)
    });
    log.info(emoji.get('ok'))
  });

  // CASK
  log.info('installing brew casks', emoji.get('coffee'))
  config.cask.map(function(item){
    log.info('brew cask install', item)
    command('. lib_sh/echos.sh && . lib_sh/requirers.sh && require_cask ' + item, __dirname, function(err, out) {
      if(err) log.error(emoji.get('fire'), err)
    });
    log.info(emoji.get('ok'))
  });

  log.info('Alright, cleaning up homebrew cache...')
  command('brew cleanup > /dev/null 2>&1')
  log.info('all clean')


  // GEM
  log.info('installing gems', emoji.get('coffee'))
  config.gem.map(function(item){
    log.info('gem install', item);
    command('. lib_sh/echos.sh && . lib_sh/requirers.sh && require_gem ' + item, __dirname, function(err, out) {
      if(err) log.error(emoji.get('fire'), err)
    });
    log.info(emoji.get('ok'))
  });

  // NPM
  log.info('installing npm globals', emoji.get('coffee'))
  config.npm.map(function(item){
    log.info('npm install', item);
    command('. lib_sh/echos.sh && . lib_sh/requirers.sh && require_npm ' + item, __dirname, function(err, out) {
      if(err) log.error(emoji.get('fire'), err)
    });
    log.info(emoji.get('ok'))
  });
});
