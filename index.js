import { confirm } from "@inquirer/prompts";
import * as emoji from "node-emoji";
import fs from "fs";
import command from "./lib_node/command.js";
import { dirname } from "path";
import { fileURLToPath } from "url";

const __dirname = dirname(fileURLToPath(import.meta.url));

async function run() {
  const answers = await confirm({
    message: "Do you want to use gitshots?",
    default: false,
  });

  if (answers) {
    // additional brew packages needed to support gitshots
    const brewConfig = (await import("./software/brew.js")).default;
    brewConfig.packages.push("imagemagick", "imagesnap");
    // ensure ~/.gitshots exists
    await command("mkdir -p ~/.gitshots", __dirname);
    // add post-commit hook
    await command(
      "cp ./.git_template/hooks/gitshot-pc ./.git_template/hooks/post-commit",
      __dirname,
    );
  } else {
    if (fs.existsSync("./.git_template/hooks/post-commit")) {
      // disable post-commit (in case we are undoing the git-shots enable)
      // TODO: examine and remove/comment out the file content with the git shots bit
      await command(
        "mv ./.git_template/hooks/post-commit ./.git_template/hooks/disabled-pc",
        __dirname,
      );
    }
  }

  // Check for .BrewFile in homedir
  const brewFileExists = fs.existsSync(path.join(__dirname, "homedir", ".BrewFile"));
  let brewFilePackages = [];
  
  if (brewFileExists) {
    const brewFileContent = fs.readFileSync(path.join(__dirname, "homedir", ".BrewFile"), "utf8");
    brewFilePackages = brewFileContent
      .split("\n")
      .map(line => line.trim())
      .filter(line => line && !line.startsWith("#"));
  }

  const tasks = [];

  // Dynamically read all software configuration files
  const softwareFiles = [
    "brew.js",
    "cask.js", 
    "npm.js",
    "mas.js",
    "gem.js"
  ];

  for (const file of softwareFiles) {
    try {
      const config = (await import(`./software/${file}`)).default;
      
      const shouldInstall = await confirm({
        message: `Do you want to install ${config.name}?`,
        default: false,
      });

      if (!shouldInstall) {
        console.log(`Skipping ${config.name} installation`);
        continue;
      }

      // Combine config packages with .BrewFile packages for brew type
      let packages = config.packages;
      if (config.type === "brew" && brewFilePackages.length > 0) {
        packages = [...new Set([...config.packages, ...brewFilePackages])];
        console.log(`Found ${brewFilePackages.length} additional packages in .BrewFile`);
      }

      if (packages && packages.length) {
        tasks.push(async () => {
          console.info(emoji.get("coffee"), ` installing ${config.type} packages`);
        });
        
        packages.forEach((item) => {
          tasks.push(async () => {
            console.info(`${config.type}:`, item);
            try {
              await command(
                `. lib_sh/echos.sh && . lib_sh/requirers.sh && require_` +
                  config.type +
                  ` ` +
                  item,
                __dirname,
              );
            } catch (err) {
              console.error(emoji.get("fire"), err, err.stderr);
            }
          });
        });
      } else {
        tasks.push(async () => {
          console.info(emoji.get("coffee"), `${config.type} has no packages`);
        });
      }
    } catch (error) {
      console.error(`Error loading software configuration from ${file}:`, error.message);
    }
  }

  for (const task of tasks) {
    await task();
  }
  console.log("package install complete");
}

run();
