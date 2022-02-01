# Getting started

How to spin up the theme and development.

## General 

Main idea of this project it to reverse the logic of a standard WordPress theme, which loads wires the view files according to the [Template Hierarchy](https://wphierarchy.com/).

This theme has a singular entry point of `index.php` which routes all the traffic though the theme core and loads a specific template from `src/views/` using View Composers. This means templates are not responsible of processing the logic anymore, but instead they're loaded by the internal classes when needed.

## Theme structure

```text
.
├── .tools/             # Internal Node package helping with development
├── config/
│   ├── acf-json/       # Local copy of ACF groups
│   ├── *.json          # Various configuration files, like colors or typography
│   └── *.php           # Bootstrap configuration files
├── src/
│   ├── assets/         # JS, SCSS and Image development files
│   ├── classes/        # Theme PHP files
│   └── views/          # Template files, loaded according to the WP template hierarchy
├── functions.php       # Theme bootstrap file
├── index.php           # Entry file, not editable
└── style.css           # Theme manifest, not loaded on front-end
```

## Development

### Install dependencies

```bash
$ composer install && npm install
```

### Build the static assets

This will transform SCSS into CSS, JS ESNext into older syntax and optimize images.

```bash
$ npm run build
```

Run the following script for processing styles, JS, and images on the fly whenever any change occur to any of those files.

```bash
$ npm run start
```

Linting and testing the code:


```bash
$ npm run lint
$ composer lint
$ composer test
```

## Deployment

Run this script to generate production-optimized assets.

```bash
$ npm run build:production
```

Migrate the files according to `.git-ftp-ignore` file or use automated Github workflow.

## Tools

To run the tools:

```bash
$ npx tools
```

or

```bash
$ yarn tools
```

This command will display help by default.

### Options

| Name   | Alias | Descrition                                                                                                 |
| ------ | ----- | ---------------------------------------------------------------------------------------------------------- |
| --help | -h    | Displays general help (this is a default behavior, the help will be displayed even without any parameter). |

### Available commands

#### `generate <what>`

This command will generate `theme.json` file and remporary sass variables file from config files.
Both commands will ran as `prebuild` and `postbuild` script, so there is no need to use them when using `npm run build` or `yarn build`.

#### Parameters

| Name      | Descrition                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
| --------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| &lt;what> | **Required.** <br> Possible values: \['sass-vars', 'theme-json'] <br> This tells the script what to generate. <br> `theme.json` should be generated in production environment (it happens automatically after `build` script). <br> Sass vars are not needed in production, but they are used DURING the build, so they need to be generated BEFORE runnign `start` script during development. Sass vars will be automatically generated BEFORE `build` script. |

#### Example
```bash
$ yarn tools generate theme-json
$ npx tools generate sass-vars -w
```

**Note:** For development it's advised to run `yarn tools generate sass-vars -w` (in watch mode) in one terminal window and then run `yarn start` in second window, so that any change to config files will cause regeneration of the sass vars file which will then cause the rebuild of the assets.

#### Options

| Name    | Alias | Descrition                                                                                                                                                        |
| ------- | ----- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| --watch | -w    | This flag will start the script in watch mode, watching changes in `config` directory and rebuilding selected json file each time any of the config files change. |

## Config files
