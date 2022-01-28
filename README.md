# DoW Starter Theme

Department of Web Starter Theme

## Strings to replace

-   `DoW Starter Theme` - Full package name
-   `dow-starter-theme` - Textdomain, node package name etc.
-   `dow/starter-theme` - Composer package name
-   `DoWStarterTheme` - PHP namespace
-   `dowst` - Variables/functions prefix

## Tools

To run the tools:

```console
npx tools
```

or

```console
yarn tools
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
```console
yarn tools generate theme-json
npx tools generate sass-vars -w
```

**Note:** For development it's advised to run `yarn tools generate sass-vars -w` (in watch mode) in one terminal window and then run `yarn start` in second window, so that any change to config files will cause regeneration of the sass vars file which will then cause the rebuild of the assets.

#### Options

| Name    | Alias | Descrition                                                                                                                                                        |
| ------- | ----- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| --watch | -w    | This flag will start the script in watch mode, watching changes in `config` directory and rebuilding selected json file each time any of the config files change. |

## Config files

Config files need to use a `.json` extension if used in `js`. Those used in php only can be `.json`, `.php`, `.xml`, `.yml` or `.ini`.

### `acf`

Optional ACF configuration.

### Options:

| Option name               | Type          | Descrition                                                                                                                                                                                               |
| ------------------------- | ------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| option-pages              | array         | Array of page data. Each page will be registered as an ACF options page.                                                                                                                                 |
| options-pages\[]          | array\|string | Single page item can be a string or an array. If string is passed, it will be used as a page title, `themes.php` will be used as parent. If it's an array, ti needs to have a `title` and `parent` keys. |
| options-pages[]['title']  | string        | Page title.                                                                                                                                                                                              |
| options-pages[]['parent'] | string        | Slug of a WordPress page to be used as parent.                                                                                                                                                           |
| location                  | string        | Json save location. **Default:** `config/acf-json`                                                                                                                                                       |

#### Example

```php
return [
    'option-pages' => [
        __('Theme Options', 'dow-starter-theme'),
        [
            'title' => __('Media Options', 'dow-starter-theme'),
            'parent' => 'upload.php'
        ]
    ],
]
```

### `blocks`

This file contains block loading configuration.

#### Options

| Option name      | Type   | Descrition                                           |
| ---------------- | ------ | ---------------------------------------------------- |
| location         | string | Blocks directory location. **Default:** `src/blocks` |
| templateFilename | string | Block template filename. **Default:** `template.php` |
| styleFilename    | string | Block style filename. **Default:** `style.scss`      |

#### Example

```php
return [
    'location' => 'src/blocks',
]
```

### `classes`

Classes configuration.

#### Options

| Option name | Type      | Descrition                                                                                                                                                                                                                                                                 |
| ----------- | --------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| general     | string\[] | Array of classes to be instantiated during bootstraping. These class instances can then be accessed with `\DoWStarterTheme\Core\Theme::getService($className)`. If the class uses `\Micropackage\DocHooks\HookTrait`, the `add_hooks` method will be called automatically. |
| widgets     | string\[] | Widget classes. These will be automatically registered using [`register_widget`](https://developer.wordpress.org/reference/functions/register_widget)                                                                                                                      |

#### Example

```php
return [
    'general' => [
        DoWStarterTheme\Core\Layout::class,
        DoWStarterTheme\Core\TemplateFilters::class,
    ],
    'widgets' => [
        DoWStarterTheme\Widgets\SocialLinksWidget::class,
    ],
]
```
