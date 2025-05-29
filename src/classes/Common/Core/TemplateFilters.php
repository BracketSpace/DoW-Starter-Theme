<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Core;

use DoWStarterTheme\Common\Config\Config;
use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Common\Helpers\Layout;
use DoWStarterTheme\Deps\Micropackage\Filesystem\Filesystem;
use WP_Post;
use WP_Theme;

/**
 * TemplateFilters trait
 */
class TemplateFilters implements Hookable
{
    /**
     * Template types
     *
     * @var array<string>
     */
    private $templateTypes = [
        '404',
        'archive',
        'attachment',
        'author',
        'category',
        'date',
        'embed',
        'frontpage',
        'home',
        'index',
        'page',
        'paged',
        'privacypolicy',
        'search',
        'single',
        'singular',
        'tag',
        'taxonomy',
    ];

    /**
     * Hooks `templateHierarchy` method for each template type filter.
     *
     * @param Config     $config Config instance.
     * @param Filesystem $fs     Filesystem instance.
     * @param Layout     $layout Layout helper instance.
     */
    public function __construct(
        private Config $config,
        private Filesystem $fs,
        private Layout $layout,
    ) {
        foreach ($this->templateTypes as $type) {
            add_filter("{$type}_template_hierarchy", [$this, 'filterTemplateHierarchy']);
        }
    }

    /**
     * Determines the view to include.
     *
     * @filter template_include
     *
     * @param  string $file Template filename.
     * @return string
     */
    public function filterTemplateInclude(string $file): string
    {
        $viewsPath = $this->fs->path('src/views');
        $extension = str_replace('.', '\.', $this->config->getString('view.extension') ?? '.php');
        $path = wp_normalize_path($file);

        $template = (string)preg_replace(
            "/{$extension}$/",
            '',
            str_replace([$viewsPath, '/'], ['', '.'], $path)
        );

        $this->layout->setTemplate($template);

        // Always return path to index.php - it's the entry point for the entire theme.
        return $this->fs->path('index.php');
    }

    /**
     * Adds views path to template files.
     *
     * @param  array<string> $files Template files.
     * @return array<string>
     */
    public function filterTemplateHierarchy(array $files): array
    {
        $location = trim($this->config->getString('view.location') ?? '', '');

        foreach ($files as &$file) {
            // Prefix each file with views location path.
            $file = "{$location}/{$file}";
        }

        return $files;
    }

    /**
     *
     * Adds support for page templates inside `src/views` directory.
     *
     * @filter theme_templates
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     *
     * @param  array<string, string> $templates List of page templates.
     * @param  WP_Theme              $theme     WP_Theme instance.
     * @param  WP_Post|null          $post      WP_POst instance.
     * @param  string                $postType  Post type.
     * @return array<string, string>
     */
    public function filterThemeTemplates(array $templates, WP_Theme $theme, ?WP_Post $post, string $postType): array
    {
        return array_unique(
            array_merge(
                $templates,
                $this->getTemplates($postType)
            )
        );
    }

    /**
     * Gets page templates for given post type.
     *
     * @param  string $postType Optional post type.
     * @return array<string, string> List of page templates.
     */
    protected function getTemplates(?string $postType = null): array
    {
        $cacheKey = 'dow-starter-theme/post-templates';
        $templates = wp_cache_get($cacheKey, 'themes');

        if (is_array($templates)) {
            return $templates[$postType] ?? [];
        }

        $paths = $this->listFiles();
        $templates = [];

        foreach ($paths as $path) {
            $content = $this->fs->get_contents($path);

            if (! is_string($content)) {
                continue;
            }

            if (preg_match('|Template Name:(.*)$|mi', $content, $header) !== 1) {
                continue;
            }

            $types = preg_match('|Template Post Type:(.*)$|mi', $content, $types) === 1
                ? explode(',', _cleanup_header_comment($types[1]))
                : ['page'];

            foreach ($types as $type) {
                $type = sanitize_key($type);

                if (! isset($templates[$type])) {
                    $templates[$type] = [];
                }

                $templates[$type][$path] = _cleanup_header_comment($header[1]);
            }
        }

        wp_cache_add($cacheKey, $templates, 'themes');

        return $templates[$postType] ?? [];
    }

    /**
     * Recursively lists all files inside `src/views` directory.
     *
     * @param  string $path Current path.
     * @return array<string> List of files.
     */
    protected function listFiles(string $path = ''): array
    {
        $paths = [];

        foreach ((array)$this->fs->dirlist('src/views/' . $path) as $data) {
            if (! is_array($data)) {
                continue;
            }

            if ($data['type'] === 'd') {
                $paths = [
                    ...$paths,
                    ...$this->listFiles("{$path}/{$data['name']}"),
                ];

                continue;
            }

            if (pathinfo($data['name'], PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            $paths[] = trim("{$path}/{$data['name']}", '/');
        }

        return $paths;
    }
}
