# Software Configuration

This directory contains split configuration files for different types of software packages.

## Files

Each configuration file exports an object with the following structure:

```javascript
export default {
  name: "Display name for prompts",
  type: "package-manager-type",
  packages: [
    "package1",
    "package2",
    // ...
  ],
};
```

### Available Files

- `brew.js` - Homebrew utilities and command-line tools
- `cask.js` - Homebrew desktop applications (GUI apps)
- `npm.js` - Global NPM packages
- `mas.js` - Mac App Store applications
- `gem.js` - Ruby gems

## .BrewFile Support

You can add additional Homebrew packages by creating a `.BrewFile` in the `homedir/` directory. This file should contain one package per line, with comments starting with `#`.

Example `.BrewFile`:
```
# Additional Homebrew packages
htop
neofetch
ripgrep
fd
exa
```

## Usage

When running the installation script, you'll be prompted for each software type:

1. **Homebrew utilities** - Command-line tools and utilities
2. **Homebrew desktop apps** - GUI applications
3. **NPM global packages** - Node.js packages installed globally
4. **Mac App Store apps** - Applications from the Mac App Store
5. **Ruby gems** - Ruby packages

Each prompt allows you to choose whether to install that category of software or skip it.

## Adding Packages

To add packages to any category, simply edit the corresponding `.js` file in this directory and add the package name to the `packages` array. The packages will be automatically installed when you run the installation script and choose to install that category.

## Adding New Software Types

To add a new software type:

1. Create a new `.js` file in this directory
2. Export an object with `name`, `type`, and `packages` properties
3. Add the filename to the `softwareFiles` array in `index.js`
4. Ensure the corresponding `require_*` function exists in `lib_sh/requirers.sh`

Example new software type:
```javascript
// software/pip.js
export default {
  name: "Python packages",
  type: "pip",
  packages: [
    "requests",
    "flask",
    "pytest",
  ],
};
``` 