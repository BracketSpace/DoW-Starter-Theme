<?php

declare(strict_types=1);

namespace DoWStarterTheme\Integration;

use DoWStarterTheme\Core\Config;
use DoWStarterTheme\Factories\Filesystem;
use Micropackage\BlockLoader\BlockLoader;
use Micropackage\DocHooks\HookTrait;

/**
 * ACF class
 *
 * @phpstan-type FieldGroup array{key: string, create_gutenberg_block: bool, block_slug?: string}
 * @phpstan-type BlockParams array{supports?: array<mixed>}
 */
class ACF
{
	use HookTrait;

	/**
	 * Blocks location
	 *
	 * @var string
	 */
	protected $blocksLocation;

	/**
	 * Json location
	 *
	 * @var string
	 */
	protected $jsonLocation;

	/**
	 * Filesystem instance.
	 *
	 * @var \Micropackage\Filesystem\Filesystem
	 */
	protected $fs;

	/**
	 * Processed field group.
	 *
	 * @var FieldGroup|null
	 */
	protected $processedFieldGroup;

	/**
	 * Block field groups.
	 *
	 * @var array<string, string>
	 */
	protected $blockGroups = [];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->blocksLocation = Config::get('blocks.location', 'src/blocks');
		$this->jsonLocation = Config::get('acf.location', 'config/acf');
		$this->fs = Filesystem::get();

		BlockLoader::init(
			[
				'dir' => $this->blocksLocation,
				'blocks_dir' => $this->blocksLocation,
				'scss_dir' => $this->blocksLocation,
				'package' => 'DoW Starter Theme',
				'categories' => [
					[
						'slug' => 'dow-starter-theme',
						'title' => __('DoW Starter Theme', 'dow-starter-theme'),
					],
				],
			]
		);
	}

	/**
	 * Prepare block groups array
	 *
	 * @action acf/include_fields 20
	 *
	 * @return void
	 */
	public function prepareBlockGroups(): void
	{
		$this->blockGroups = get_option('starter-theme-trashed-acf-groups', []);
		$blocksPath = $this->fs->path($this->blocksLocation);

		/**
		 * @var array<string,string>
		 */
		$files = acf_get_local_json_files();

		foreach ($files as $key => $file) {
			if (strpos($file, $blocksPath) !== 0) {
				continue;
			}

			$this->blockGroups[$key] = dirname($file);
		}
	}

	/**
	 * Registers ACF Theme Options page
	 *
	 * @action acf/init
	 *
	 * @return void
	 */
	public function acfInit()
	{
		if (! function_exists('acf_add_options_page')) {
			return;
		}

		$pages = Config::get('acf.option-pages');

		if (! is_array($pages)) {
			return;
		}

		foreach ($pages as $page) {
			if (is_array($page) && (! isset($page['title']) || ! isset($page['parent']))) {
				continue;
			}

			acf_add_options_page(
				[
					'page_title' => is_array($page) ? $page['title'] : $page,
					'parent_slug' => is_array($page) ? $page['parent'] : 'themes.php',
				]
			);
		}
	}

	/**
	 * Filter block params for block loader
	 *
	 * @filter micropackage/block-loader/block-params
	 *
	 * @param  BlockParams $data Block data.
	 * @return BlockParams
	 */
	public function blockParams(array $data): array
	{
		if (! isset($data['supports'])) {
			$data['supports'] = [];
		}

		// Add experimental jsx support to use <InnerBlocks /> in ACF blocks.
		$data['supports']['__experimental_jsx'] = true;

		return $data;
	}

	/**
	 * Store processed field group in local variable
	 *
	 * @action acf/update_field_group  1
	 * @action acf/trash_field_group   1
	 * @action acf/untrash_field_group 1
	 * @action acf/delete_field_group  1
	 *
	 * @param FieldGroup $fieldGroup Field group config.
	 * @return void
	 */
	public function storeFieldGroup(array $fieldGroup): void
	{
		$this->processedFieldGroup = $fieldGroup;
	}

	/**
	 * Store processed field group in local variable
	 *
	 * @action acf/update_field_group  20
	 * @action acf/trash_field_group   20
	 * @action acf/untrash_field_group 20
	 * @action acf/delete_field_group  20
	 *
	 * @return void
	 */
	public function resetFieldGroup(): void
	{
		$this->processedFieldGroup = null;
	}

	/**
	 * Store trashed group in options
	 *
	 * @action acf/trash_field_group
	 *
	 * @param FieldGroup $fieldGroup Field group config.
	 * @return void
	 */
	public function storeTrashedGroup(array $fieldGroup): void
	{
		$key = (string)preg_replace('/__trashed$/', '', $fieldGroup['key']);

		if (!array_key_exists($key, $this->blockGroups)) {
			return;
		}

		$trashed = get_option('starter-theme-trashed-acf-groups', []);

		if (! array_key_exists($key, $trashed)) {
			return;
		}

		$trashed[$key] = $this->blockGroups[$key];
		update_option('starter-theme-trashed-acf-groups', $trashed);
	}

	/**
	 * Store trashed group in options
	 *
	 * @action acf/delete_field_group
	 * @action acf/untrash_field_group
	 *
	 * @param FieldGroup $fieldGroup Field group config.
	 * @return void
	 */
	public function removeDeletedGroup(array $fieldGroup): void
	{
		$key = (string)preg_replace('/__trashed$/', '', $fieldGroup['key']);

		if (!array_key_exists($key, $this->blockGroups)) {
			return;
		}

		$trashed = get_option('starter-theme-trashed-acf-groups', []);

		if (!array_key_exists($key, $trashed)) {
			return;
		}

		unset($trashed[$key]);
		update_option('starter-theme-trashed-acf-groups', $trashed);
	}

	/**
	 * Sets the ACF JSON saving point
	 *
	 * @filter acf/settings/save_json
	 *
	 * @return string Saving point path.
	 */
	public function acfJsonSavePoint(): string
	{
		if (isset($this->processedFieldGroup)) {
			if (
				$this->processedFieldGroup['create_gutenberg_block'] &&
				isset($this->processedFieldGroup['block_slug']) &&
				$this->processedFieldGroup['block_slug'] !== ''
			) {
				$dir = "{$this->blocksLocation}/{$this->processedFieldGroup['block_slug']}";

				if (! $this->fs->exists($dir)) {
					$this->fs->mkdir($dir);
				}

				return $this->fs->path($dir);
			}

			$key = (string)preg_replace('/__trashed$/', '', $this->processedFieldGroup['key']);

			if (array_key_exists($key, $this->blockGroups)) {
				return $this->blockGroups[$key];
			}
		}

		return $this->fs->path($this->jsonLocation);
	}

	/**
	 * Sets the ACF JSON loading point
	 *
	 * @filter acf/settings/load_json
	 *
	 * @return array<string>
	 */
	public function acfJsonLoadPoint()
	{
		$dirs = [
			$this->fs->path($this->jsonLocation),
		];

		$blocks = $this->fs->dirlist($this->blocksLocation);

		if (is_array($blocks)) {
			foreach ($blocks as $item) {
				if (!$this->fs->is_dir("{$this->blocksLocation}/{$item['name']}")) {
					continue;
				}

				$dirs[] = $this->fs->path("{$this->blocksLocation}/{$item['name']}");
			}
		}

		return $dirs;
	}

	/**
	 * Filter block dir
	 *
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 *
	 * @filter micropackage/acf-block-creator/block-template-dir
	 * @filter micropackage/acf-block-creator/block-style-dir
	 *
	 * @param  string $dir  Block template dir.
	 * @param  string $slug Block slug.
	 * @return string
	 */
	public function blocksDir(string $dir, string $slug): string
	{
		return "{$this->blocksLocation}/{$slug}";
	}

	/**
	 * Filter block template filename
	 *
	 * @filter micropackage/acf-block-creator/block-template-file
	 *
	 * @return string
	 */
	public function templateFile(): string
	{
		return Config::get('blocks.templateFilename', 'template.php');
	}

	/**
	 * Filter block style filename
	 *
	 * @filter micropackage/acf-block-creator/block-style-file
	 *
	 * @return string
	 */
	public function styleFile(): string
	{
		return Config::get('blocks.styleFilename', 'style.scss');
	}
}
