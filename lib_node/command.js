var exec = require('child_process').exec;
module.exports = function _command(cmd, dir, cb) {
  exec(cmd, {
    cwd: dir || __dirname
  }, function(err, stdout, stderr) {
    if (err) {
      console.error(err, stdout, stderr);
    }
    cb(err, stdout.split('\n').join(''), stderr);
  });
};
