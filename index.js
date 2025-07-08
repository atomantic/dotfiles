import { confirm } from "@inquirer/prompts";
import * as emoji from "node-emoji";
import fs from "fs";
import series from "async.series";
import command from "./lib_node/command.js";
import config from "./config.js";

async function run() {
  const answers = await confirm({
    message: "Do you want to use gitshots?",
    default: false,
  });

  if (answers) {
    // additional brew packages needed to support gitshots
    config.default.brew.push("imagemagick", "imagesnap");
    // ensure ~/.gitshots exists
    await command.default("mkdir -p ~/.gitshots", __dirname);
    // add post-commit hook
    await command.default(
      "cp ./.git_template/hooks/gitshot-pc ./.git_template/hooks/post-commit",
      __dirname,
    );
  } else {
    if (fs.existsSync("./.git_template/hooks/post-commit")) {
      // disable post-commit (in case we are undoing the git-shots enable)
      // TODO: examine and remove/comment out the file content with the git shots bit
      await command.default(
        "mv ./.git_template/hooks/post-commit ./.git_template/hooks/disabled-pc",
        __dirname,
      );
    }
  }

  const packagesAnswer = await confirm({
    message: "Do you want to install packages from config.js?",
    default: false,
  });

  if (!packagesAnswer) {
    return console.log("skipping package installs");
  }

  const tasks = [];

  ["brew", "cask", "npm", "gem", "mas"].forEach((type) => {
    if (config.default[type] && config.default[type].length) {
      tasks.push(async (cb) => {
        console.info(emoji.get("coffee"), " installing " + type + " packages");
        cb();
      });
      config.default[type].forEach((item) => {
        tasks.push(async (cb) => {
          console.info(type + ":", item);
          try {
            await command.default(
              `. lib_sh/echos.sh && . lib_sh/requirers.sh && require_` +
                type +
                ` ` +
                item,
              __dirname,
            );
          } catch (err) {
            console.error(emoji.get("fire"), err, err.stderr);
          }
          cb();
        });
      });
    } else {
      tasks.push(async (cb) => {
        console.info(emoji.get("coffee"), type + " has no packages");
        cb();
      });
    }
  });

  series(tasks, function (err, results) {
    console.log("package install complete");
  });
}

run();
