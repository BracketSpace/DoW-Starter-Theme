<?php

declare(strict_types=1);

namespace DoWStarterTheme\Common\Abstracts;

use DoWStarterTheme\Common\Contracts\Hookable;
use DoWStarterTheme\Deps\Illuminate\Support\Str;

/**
 * @phpstan-type ScriptMeta array{dependencies: array<string>, version: string}
 */
abstract class AdminPage extends Sluggable implements Hookable
{
    /**
     * Suffix removed from class name when making a slug.
     */
    protected static string $suffix = 'Page';

    /**
     * Stores page hook suffix.
     *
     * @return string|null
     */
    protected ?string $id = null;

    /**
     * Returns optional parent page slug.
     *
     * @return string|null
     */
    public static function getParent(): ?string
    {
        return null;
    }

    /**
     * Returns page URL.
     *
     * @return string
     */
    public static function getUrl(): string
    {
        $base = static::getParent() ?? 'admin.php';

        return admin_url("{$base}?page=" . self::getSlug());
    }

    /**
     * Returns page title.
     *
     * @return string
     */
    protected function getTitle(): string
    {
        if (! (bool)self::getData('name')) {
            self::setData('name', Str::singular(Str::title(str_replace('-', ' ', static::getSlug()))));
        }

        /**
         * @var string
         */
        return self::getData('name');
    }

    /**
     * Returns menu title. Defaults to page title.
     *
     * @return string
     */
    protected function getMenuTitle(): string
    {
        return $this->getTitle();
    }

    /**
     * Returns capability required to access the page.
     *
     * @return string
     */
    protected function getCapability(): string
    {
        return 'manage_options';
    }

    /**
     * Returns menu position for the page.
     *
     * @return int|null
     */
    protected function getPosition(): ?int
    {
        return null;
    }

    /**
     * Returns an array of assets to enqueue for the page.
     *
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [];
    }

    /**
     * Gets menu icon url.
     *
     * @return string|null
     */
    protected function getIconUrl(): ?string
    {
        return null;
    }

    /**
     * Adds management page to WordPress admin menu.
     *
     * @action admin_menu
     *
     * @return void
     */
    public function adminMenu(): void
    {
        $parent = static::getParent();

        if ($parent === null) {
            $this->id = add_menu_page(
                $this->getTitle(),
                $this->getMenuTitle(),
                $this->getCapability(),
                self::getSlug(),
                [$this, 'render'],
                $this->getIconUrl() ?? 'dashicons-admin-generic',
                $this->getPosition()
            );
        } else {
            $id = add_submenu_page(
                $parent,
                $this->getTitle(),
                $this->getMenuTitle(),
                $this->getCapability(),
                self::getSlug(),
                [$this, 'render'],
                $this->getPosition()
            );

            if (is_string($id)) {
                $this->id = $id;
            }
        }
    }

    /**
     * Enqueues assets.
     *
     * @action admin_enqueue_scripts
     *
     * @return void
     */
    public function enqueueAssets(): void
    {
        if (get_current_screen()?->id !== $this->id) {
            return;
        }

        foreach ($this->getAssets() as $asset) {
            $asset->register();
            $asset->enqueue();
        }
    }

    /**
     * Renders the page.
     *
     * @return void
     */
    abstract public function render(): void;
}
