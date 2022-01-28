# DoW Starter Theme

Department of Web Starter Theme

## Strings to replace

-   `dowst` - textdomain
-   `DoW Starter Theme` - full package name
-   `dow/starter-theme` - composer package name
-   `DoWStarterTheme` - PHP namespace
-   `dowst_` - PHP function prefix

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

| Option name | Type   | Descrition                 |
| ----------- | ------ | -------------------------- |
| location    | string | Blocks directory location. |

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
