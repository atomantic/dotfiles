import { exec } from "child_process";
export default function command(cmd, dir) {
  return new Promise((resolve, reject) => {
    exec(
      cmd,
      {
        cwd: dir || __dirname,
      },
      function (err, stdout, stderr) {
        if (err) {
          console.error(err, stdout, stderr);
          reject(err);
        } else {
          resolve(stdout.split("\n").join(""));
        }
      }
    );
  });
}
